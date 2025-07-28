<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Pastikan ini diimpor

class ReviewController extends Controller
{
    /**
     * Store a new product review and complete the associated order.
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

        // Menggunakan transaksi database untuk memastikan atomicity
        DB::beginTransaction();
        try {
            $order = Order::with('orderItems')->find($orderId); // Eager load orderItems

            // 2. Otorisasi: Pastikan pengguna adalah pemilik pesanan
            if (!$order || $order->user_id !== $userId) {
                DB::rollBack();
                Log::warning('Unauthorized review submission attempt or order not found.', ['order_id' => $orderId, 'user_id' => $userId]);
                return response()->json(['success' => false, 'message' => 'Tindakan tidak diizinkan. Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan ini.'], 403);
            }

            // 3. Status Pesanan: Pastikan pesanan sudah berstatus 'Delivered'
            if ($order->status !== 'Delivered') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Pesanan tidak dapat direview atau diselesaikan dari status saat ini (' . $order->status . '). Hanya pesanan yang sudah Delivered yang bisa diselesaikan.'], 400);
            }

            // 4. Periksa apakah sudah ada review untuk produk ini dalam pesanan ini
            // Ambil product_id dari item pertama di pesanan. Asumsi satu review per pesanan.
            $productIdToReview = $order->orderItems->first()->product_id ?? null;

            if (is_null($productIdToReview)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Tidak ada produk yang ditemukan dalam pesanan ini untuk direview.'], 400);
            }

            // Membangun query untuk mencari review yang sudah ada
            $query = ProductReview::where('user_id', $userId)
                                  ->where('product_id', $productIdToReview);

            // Jika kolom 'order_id' ada di tabel product_reviews, tambahkan ke kondisi query
            if (Schema::hasColumn('product_reviews', 'order_id')) {
                $query->where('order_id', $orderId);
            }

            $existingReview = $query->first();

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
                    'product_id' => $productIdToReview,
                    'rating' => $rating,
                    'comment' => $comment,
                ];
                // Jika kolom 'order_id' ada, tambahkan ke data review
                if (Schema::hasColumn('product_reviews', 'order_id')) {
                    $reviewData['order_id'] = $orderId;
                }
                ProductReview::create($reviewData);
            }

            // 6. Update Status Order menjadi 'Completed'
            $order->status = 'Completed';
            $order->save();

            // Commit transaksi jika semua operasi berhasil
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Review berhasil dikirim dan pesanan diselesaikan!']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validasi error dari $request->validate()
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            // Tangani error umum lainnya
            DB::rollBack();
            Log::error('Review submission and order completion failed: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'user_id' => $userId,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Tambahkan trace untuk debugging lebih lanjut
                'request_data' => $request->all()
            ]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan internal. Silakan coba lagi. Mohon hubungi administrator jika masalah berlanjut.'], 500);
        }
    }
}