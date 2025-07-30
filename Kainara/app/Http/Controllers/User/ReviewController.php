<?php

namespace App\Http\Controllers\User; // Keep this namespace if your ReviewController is in User namespace

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ProductReview; // Make sure this model is correctly named ProductReview
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReviewController extends Controller
{
    /**
     * Store a new product review. The order status update is handled by OrderController@completeOrder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $orderId = $request->input('order_id');
        $rating = $request->input('rating');
        $comment = $request->input('comment');
        $userId = Auth::id();

        DB::beginTransaction();
        try {
            $order = Order::with('orderItems.product')->find($orderId); // Eager load orderItems and their products

            // 2. Otorisasi: Pastikan pengguna adalah pemilik pesanan
            if (!$order || $order->user_id !== $userId) {
                DB::rollBack();
                Log::warning('Unauthorized review submission attempt or order not found.', ['order_id' => $orderId, 'user_id' => $userId]);
                return response()->json(['success' => false, 'message' => 'Tindakan tidak diizinkan. Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan ini.'], 403);
            }

            // 3. Status Pesanan: Pastikan pesanan sudah berstatus 'Delivered'
            // This check is duplicated with the `completeOrder` method, but it's good to have it here
            // to prevent creating a review for an invalid order status prematurely.
            if ($order->status !== 'Delivered') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Pesanan tidak dapat direview. Hanya pesanan yang sudah Delivered yang bisa direview.'], 400);
            }

            // 4. Periksa apakah sudah ada review untuk **setiap produk** dalam pesanan ini.
            // Loop melalui setiap item dalam pesanan untuk membuat atau memperbarui review produk.
            // Asumsi: Setiap item dalam pesanan bisa memiliki review terpisah, atau jika desainnya
            // 1 review per order, maka hanya ambil produk pertama atau produk utama jika ada.
            // Disesuaikan dengan asumsi yang Anda miliki di Blade: 1 review per order untuk produk pertama.
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

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            // **Important:** We are NOT setting order status to 'Completed' here.
            // The JavaScript on myorder.blade.php will call the 'order.complete' route
            // *after* receiving a success response from this review.store route.
            return response()->json(['success' => true, 'message' => 'Review berhasil dikirim!']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Review submission failed: ' . $e->getMessage(), [
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