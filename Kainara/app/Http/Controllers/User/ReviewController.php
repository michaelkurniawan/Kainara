<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product; // Add this line to use the Product model
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $skipReview = $request->boolean('skip_review');

            // 1. Validate Input
            if (!$skipReview) {
                $request->validate([
                    'order_id' => 'required|exists:orders,id',
                    'product_id' => 'required|exists:products,id', // Added validation for product_id
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

            $order = Order::find($orderId); // No need to load orderItems yet

            // 2. Otorisasi: Pastikan pengguna adalah pemilik pesanan
            if (!$order || $order->user_id !== $userId) {
                DB::rollBack();
                Log::warning('Unauthorized review submission attempt or order not found.', ['order_id' => $orderId, 'user_id' => $userId]);
                return back()->with('notification', [
                    'type' => 'error',
                    'title' => 'Unauthorized!',
                    'message' => 'The order was not found or you do not have access to it.',
                    'hasActions' => false
                ]);
            }

            // 3. Status Pesanan: Pastikan pesanan sudah berstatus 'Delivered'
            if ($order->status !== 'Delivered') {
                DB::rollBack();
                return back()->with('notification', [
                    'type' => 'error',
                    'title' => 'Cannot Complete Order!',
                    'message' => 'The order status must be "Delivered" to complete.',
                    'hasActions' => false
                ]);
            }

            if (!$skipReview) {
                $rating = $request->input('rating');
                $comment = $request->input('comment');
                $productId = $request->input('product_id'); // Get the product_id from the request

                // 4. Periksa apakah produk ada dalam pesanan ini
                $isProductInOrder = $order->orderItems()->where('product_id', $productId)->exists();
                if (!$isProductInOrder) {
                    DB::rollBack();
                    return back()->with('notification', [
                        'type' => 'error',
                        'title' => 'Error!',
                        'message' => 'The product you are trying to review is not part of this order.',
                        'hasActions' => false
                    ]);
                }
                
                // 5. Check for an existing review for this user, order, and product combination
                $existingReview = ProductReview::where('user_id', $userId)
                                              ->where('order_id', $orderId) // Link review to order
                                              ->where('product_id', $productId)
                                              ->first();

                // 6. Simpan atau Perbarui Review
                if ($existingReview) {
                    $existingReview->rating = $rating;
                    $existingReview->comment = $comment;
                    $existingReview->save();
                } else {
                    $reviewData = [
                        'user_id' => $userId,
                        'product_id' => $productId,
                        'order_id' => $orderId,
                        'rating' => $rating,
                        'comment' => $comment,
                    ];
                    ProductReview::create($reviewData);
                }
            }

            // 7. Update Order Status to 'Completed'
            $order->status = 'Completed';
            $order->save();
            DB::commit();

            // Berikan respons redirect dengan notifikasi
            if (!$skipReview) {
                return redirect()->route('profile.index', ['#order-history'])->with('notification', [
                    'type' => 'success',
                    'title' => 'Review Submitted!',
                    'message' => 'Thank you for your review! Your order has been completed.',
                    'hasActions' => false
                ]);
            } else {
                return redirect()->route('profile.index', ['#order-history'])->with('notification', [
                    'type' => 'info',
                    'title' => 'Order Completed!',
                    'message' => 'Your order has been completed without a review.',
                    'hasActions' => false
                ]);
            }

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->with('notification', [
                'type' => 'error',
                'title' => 'Validation Failed!',
                'message' => $e->validator->errors()->first(),
                'hasActions' => false
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order completion/review submission failed: ' . $e->getMessage(), [
                'order_id' => $request->input('order_id'),
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
            ]);
            return back()->with('notification', [
                'type' => 'error',
                'title' => 'Error!',
                'message' => 'An unexpected error occurred. Please try again.',
                'hasActions' => false
            ]);
        }
    }
}