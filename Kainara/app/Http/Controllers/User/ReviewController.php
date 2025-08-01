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
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $skipReview = filter_var($request->input('skip_review'), FILTER_VALIDATE_BOOLEAN);

            if (!$skipReview) {
                $request->validate([
                    'order_id' => 'required|exists:orders,id',
                    'rating' => 'required|integer|min:1|max:5',
                    'comment' => 'nullable|string|max:1000',
                ]);
            } else {
                $request->validate([
                    'order_id' => 'required|exists:orders,id',
                ]);
            }

            $orderId = $request->input('order_id');
            $userId = Auth::id();

            $order = Order::with('orderItems.product')->find($orderId);

            if (!$order || $order->user_id !== $userId) {
                DB::rollBack();
                Log::warning('Unauthorized review submission attempt or order not found.', ['order_id' => $orderId, 'user_id' => $userId]);
                return response()->json(['success' => false, 'message' => 'Unauthorized action. Order not found or you do not have access to this order.'], 403)
                    ->with('notification', [
                        'type' => 'error',
                        'title' => 'Akses Ditolak',
                        'message' => 'Anda tidak diizinkan untuk melakukan tindakan ini.'
                    ]);
            }

            if ($order->status !== 'Delivered') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Order cannot be processed. Only Delivered orders can be completed.'], 400)
                    ->with('notification', [
                        'type' => 'error',
                        'title' => 'Gagal Menyelesaikan Pesanan',
                        'message' => 'Pesanan ini tidak dapat diselesaikan. Pastikan status pesanan adalah "Delivered".'
                    ]);
            }

            if (!$skipReview) {
                $rating = $request->input('rating');
                $comment = $request->input('comment');

                $productToReview = $order->orderItems->first()->product ?? null;

                if (is_null($productToReview)) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'No product found in this order to review.'], 400);
                }

                $existingReviewQuery = ProductReview::where('user_id', $userId)
                                                    ->where('product_id', $productToReview->id);

                if (Schema::hasColumn('product_reviews', 'order_id')) {
                    $existingReviewQuery->where('order_id', $orderId);
                }
                $existingReview = $existingReviewQuery->first();

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
            }

            $order->status = 'Completed';
            $order->save();

            DB::commit();

            $redirectUrl = route('profile.index', ['#order-history']);

            if (!$skipReview) {
                return response()->json(['success' => true, 'message' => 'Review successfully submitted and order completed!', 'redirect_url' => $redirectUrl])
                    ->with('notification', [
                        'type' => 'success',
                        'title' => 'Review Terkirim',
                        'message' => 'Terima kasih atas review Anda! Pesanan Anda telah berhasil diselesaikan.'
                    ]);
            } else {
                return response()->json(['success' => true, 'message' => 'Order completed without review.', 'redirect_url' => $redirectUrl])
                    ->with('notification', [
                        'type' => 'info',
                        'title' => 'Pesanan Selesai',
                        'message' => 'Pesanan Anda telah berhasil diselesaikan.'
                    ]);
            }

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422)
                ->with('notification', [
                    'type' => 'error',
                    'title' => 'Validasi Gagal',
                    'message' => 'Silakan periksa kembali input Anda: ' . $e->validator->errors()->first()
                ]);
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
            return response()->json(['success' => false, 'message' => 'An internal error occurred. Please try again. Please contact the administrator if the problem persists.'], 500)
                ->with('notification', [
                    'type' => 'error',
                    'title' => 'Kesalahan Server',
                    'message' => 'Terjadi kesalahan internal. Mohon coba lagi. Jika masalah berlanjut, hubungi administrator.'
                ]);
        }
    }
}