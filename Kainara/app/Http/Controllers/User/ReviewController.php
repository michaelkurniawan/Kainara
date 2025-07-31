<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * Store a new product review and complete the order, or just complete the order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Check if the request is to skip the review entirely
            $skipReview = $request->input('skip_review', false); // Default to false

            if (!$skipReview) {
                // 1. Validate Input only if a review is being submitted
                $request->validate([
                    'order_id' => 'required|exists:orders,id',
                    'rating' => 'required|integer|min:1|max:5', // Rating is required if not skipping
                    'comment' => 'nullable|string|max:1000',
                ]);
            } else {
                // If skipping, only order_id is strictly required
                $request->validate([
                    'order_id' => 'required|exists:orders,id',
                ]);
            }


            $orderId = $request->input('order_id');
            $userId = Auth::id();

            $order = Order::with('orderItems.product')->find($orderId); // Eager load orderItems and their products

            // 2. Otorisasi: Pastikan pengguna adalah pemilik pesanan
            if (!$order || $order->user_id !== $userId) {
                DB::rollBack();
                Log::warning('Unauthorized review submission attempt or order not found.', ['order_id' => $orderId, 'user_id' => $userId]);
                return response()->json(['success' => false, 'message' => 'Tindakan tidak diizinkan. Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan ini.'], 403);
            }

            // 3. Status Pesanan: Pastikan pesanan sudah berstatus 'Delivered'
            if ($order->status !== 'Delivered') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Pesanan tidak dapat diproses. Hanya pesanan yang sudah Delivered yang bisa diselesaikan.'], 400);
            }

            // If not skipping, proceed with review creation/update
            if (!$skipReview) {
                $rating = $request->input('rating');
                $comment = $request->input('comment');

                // 4. Determine which product to review (assuming one review per order for the first product)
                $productToReview = $order->orderItems->first()->product ?? null;

                if (is_null($productToReview)) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Tidak ada produk yang ditemukan dalam pesanan ini untuk direview.'], 400);
                }

                // Check if a review already exists for this user and product (and potentially order)
                $existingReviewQuery = ProductReview::where('user_id', $userId)
                                                    ->where('product_id', $productToReview->id);

                // If product_reviews table has 'order_id' column, use it for stricter check
                if (Schema::hasColumn('product_reviews', 'order_id')) {
                    $existingReviewQuery->where('order_id', $orderId);
                }
                $existingReview = $existingReviewQuery->first();

                // 5. Simpan atau Perbarui Review
                if ($existingReview) {
                    // Perbarui review yang sudah ada
                    $existingReview->rating = $rating;
                    $existingReview->comment = $comment;
                    $existingReview->save();
                } else {
                    // Buat review baru
                    $reviewData = [
                        'user_id' => $userId,
                        'product_id' => $productToReview->id, // Associate review with the product
                        'rating' => $rating,
                        'comment' => $comment,
                    ];
                    // Jika kolom 'order_id' ada, tambahkan ke data review
                    if (Schema::hasColumn('product_reviews', 'order_id')) {
                        $reviewData['order_id'] = $orderId;
                    }
                    ProductReview::create($reviewData);
                }
            }

            // 6. Update Order Status to 'Completed' always, regardless of review submission
            $order->status = 'Completed';
            $order->save();

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            if (!$skipReview) {
                return response()->json(['success' => true, 'message' => 'Review berhasil dikirim dan pesanan telah diselesaikan!']);
            } else {
                return response()->json(['success' => true, 'message' => 'Pesanan telah diselesaikan tanpa review.']);
            }

        } catch (ValidationException $e) { // Tangani ValidationException secara eksplisit
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        }
        catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order completion/review submission failed: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'user_id' => $userId,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan internal. Silakan coba lagi. Mohon hubungi administrator jika masalah berlanjut.'], 500);
        }
    }
}