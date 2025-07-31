<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminOrderController extends Controller
{
    const UNCOMPLETED_STATUSES = [
        'Order Confirmed',
        'Awaiting Shipment',
        'Shipped',
        'Delivered',
        'Returned',
    ];

    const ALL_STATUSES = [
        'Awaiting Payment',
        'Order Confirmed',
        'Awaiting Shipment',
        'Shipped',
        'Delivered',
        'Canceled',
        'Returned',
        'Refunded',
        'Completed'
    ];

    private const STATUS_ORDER_MAP = [
        'Awaiting Payment' => 0,
        'Order Confirmed' => 1,
        'Awaiting Shipment' => 2,
        'Shipped' => 3,
        'Delivered' => 4,
        'Completed' => 5,
    ];

    public function index(Request $request)
    {
        $filterStatus = $request->query('status');

        $ordersQuery = Order::query()->with(['user', 'address', 'orderItems.product']);

        if ($filterStatus) {
            if ($filterStatus === 'Completed') {
                $ordersQuery->where('status', 'Completed')->where('is_completed', true);
            } else if ($filterStatus === 'Uncompleted') {
                $ordersQuery->whereIn('status', self::UNCOMPLETED_STATUSES)->where('is_completed', false);
            } else {
                $ordersQuery->where('status', $filterStatus);
            }
        }

        $orders = $ordersQuery->latest()->paginate(10);

        $allStatuses = array_merge(self::ALL_STATUSES, ['Uncompleted']);

        return view('admin.orders.index', compact('orders', 'allStatuses', 'filterStatus'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Order $order)
    {
        $order->load(['user', 'address', 'orderItems.product', 'orderItems.productVariant']);
        $allStatuses = self::ALL_STATUSES;

        return view('admin.orders.show', compact('order', 'allStatuses'));
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', self::ALL_STATUSES)],
        ]);

        try {
            $oldStatus = $order->status;
            $newStatus = $request->input('status');

            $oldStatusOrder = self::STATUS_ORDER_MAP[$oldStatus] ?? null;
            $newStatusOrder = self::STATUS_ORDER_MAP[$newStatus] ?? null;

            if (
                $oldStatusOrder !== null && $newStatusOrder !== null &&
                $newStatusOrder < $oldStatusOrder
            ) {
                return redirect()->back()->withErrors(['status' => 'Status tidak bisa dikembalikan ke tahap sebelumnya.']);
            }

            if ($oldStatus === 'Order Confirmed' && $newStatus === 'Awaiting Shipment') {
                $request->validate([
                    'courier_name' => ['required', 'string', 'max:255'],
                    'tracking_number' => ['required', 'string', 'max:255'],
                ]);

                $courierName = $request->input('courier_name');
                $trackingNumber = $request->input('tracking_number');

                if (empty($order->shipping_recipient_name) || empty($order->shipping_address)) {
                    return redirect()->back()->withErrors(['status' => 'Data pengiriman tidak lengkap untuk membuat label.']);
                }

                $delivery = Delivery::create([
                    'order_id' => $order->id,
                    'courier_name' => $courierName,
                    'tracking_number' => $trackingNumber,
                    'shipped_at' => null,
                    'delivered_at' => null,
                ]);

                $pdf = Pdf::loadView('admin.orders.shipping_label_pdf', compact('order'))->setOptions([
                    'orientation' => 'landscape',
                    'defaultFont' => 'sans-serif',
                ]);
                $fileName = 'shipping_label_' . $order->id . '_' . \Illuminate\Support\Str::random(8) . '.pdf';
                $filePath = 'shipping_labels/' . $fileName;

                Storage::disk('public')->put($filePath, $pdf->output());

                $order->shipping_label = $filePath;
                $order->status = $newStatus;
                $order->save();

                return Storage::disk('public')->download($filePath, $fileName);
            }

            if ($oldStatus === 'Awaiting Shipment' && $newStatus === 'Shipped') {
                if (!$order->delivery || $order->delivery->shipped_at !== null) {
                    return redirect()->back()->withErrors(['status' => 'Informasi pengiriman tidak ditemukan atau sudah shipped.']);
                }

                $order->delivery->shipped_at = now();
                $order->delivery->save();

                $order->status = $newStatus;
                $order->save();

                return redirect()->route('admin.orders.show', $order)->with('success', 'Status pesanan berhasil diperbarui ke Shipped.');
            }

            if ($oldStatus === 'Shipped' && $newStatus === 'Delivered') {
                if (!$order->delivery) {
                    return redirect()->back()->withErrors(['status' => 'Tidak dapat memperbarui ke Delivered: Informasi pengiriman tidak ditemukan.']);
                }
                $order->delivery->delivered_at = now();
                $order->delivery->save();

                $order->status = $newStatus;
                $order->auto_complete_at = now()->addDays(2);
                $order->save();

                return redirect()->route('admin.orders.show', $order)->with('success', 'Status pesanan berhasil diperbarui ke Delivered.');
            }

            $order->status = $newStatus;

            if ($newStatus === 'Completed') {
                $order->is_completed = true;
                $order->completed_at = now();
            } else {
                $order->is_completed = false;
                $order->completed_at = null;
            }

            $order->save();

            return redirect()->route('admin.orders.show', $order)->with('success', 'Status pesanan berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui status pesanan. Pesan: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        //
    }

    public function downloadShippingLabel(Order $order)
    {
        if (!$order->shipping_label) {
            return redirect()->back()->with('error', 'Label pengiriman tidak ditemukan untuk pesanan ini.');
        }
        if (!Storage::disk('public')->exists($order->shipping_label)) {
            return redirect()->back()->with('error', 'File label pengiriman tidak ditemukan di server. Harap hubungi administrator.');
        }
        $fileName = 'shipping_label_order_' . $order->id . '.pdf';
        return Storage::disk('public')->download($order->shipping_label, $fileName);
    }
}