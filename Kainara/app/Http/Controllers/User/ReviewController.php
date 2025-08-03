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
     * Store a new product review and complete the order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Mendapatkan order_id dari request
        $orderId = $request->input('order_id');
        $userId = Auth::id();

        DB::beginTransaction();
        try {
            // 1. Ambil pesanan
            $order = Order::find($orderId);

            // 2. Otorisasi dan validasi awal
            if (!$order || $order->user_id !== $userId) {
                DB::rollBack();
                Log::warning('Unauthorized action attempt or order not found.', ['order_id' => $orderId, 'user_id' => $userId]);
                return response()->json(['success' => false, 'message' => 'Tindakan tidak diizinkan. Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan ini.'], 403);
            }

            if ($order->status !== 'Delivered') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Pesanan tidak dapat direview. Hanya pesanan yang sudah Delivered yang bisa direview.'], 400);
            }

            // 3. Tambahan Logika: Cek apakah permintaan adalah untuk SKIP review
            if ($request->has('skip_review') && $request->input('skip_review') == '1') {
                $order->status = 'Completed';
                $order->save();
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Pesanan berhasil diselesaikan tanpa review.']);
            }

            // 4. Validasi Input untuk Review (Hanya jika tidak di-skip)
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
            ]);

            $rating = $request->input('rating');
            $comment = $request->input('comment');

            // 5. Eager load orderItems dan product setelah validasi
            $order->load('orderItems.product');

            $productToReview = $order->orderItems->first()->product ?? null;

            if (is_null($productToReview)) {
                DB::rollBack();
                return redirect()->route('profile.index', ['#order-history'])->with('notification', [
                    'type' => 'info',
                    'title' => 'Order Completed!',
                    'message' => 'Your order has been completed without a review.',
                    'hasActions' => false
                ]);
            }

            // Check if a review already exists for this user and product
            $existingReviewQuery = ProductReview::where('user_id', $userId)->where('product_id', $productToReview->id);

            if (Schema::hasColumn('product_reviews', 'order_id')) {
                $existingReviewQuery->where('order_id', $orderId);
            }
            $existingReview = $existingReviewQuery->first();

            // 6. Simpan atau Perbarui Review
            if ($existingReview) {
                $existingReview->rating = $rating;
                $existingReview->comment = $comment;
                $existingReview->save();
            } else {
                $reviewData = [
                    'user_id' => $userId,
                    'product_id' => $productToReview->id,
                    'rating' => $rating,
                    'comment' => $comment,
                ];
                if (Schema::hasColumn('product_reviews', 'order_id')) {
                    $reviewData['order_id'] = $orderId;
                }
                ProductReview::create($reviewData);
            }

            // 7. Update Order Status to 'Completed' AFTER review is successfully saved
            $order->status = 'Completed';
            $order->save();

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Review berhasil dikirim dan pesanan telah diselesaikan!']);

        } catch (ValidationException $e) {
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