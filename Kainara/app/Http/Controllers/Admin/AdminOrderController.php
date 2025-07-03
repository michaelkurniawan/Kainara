<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan Anda sudah menginst


class AdminOrderController extends Controller
{
    // Definisikan status secara langsung
    const UNCOMPLETED_STATUSES = [
        'Order Confirmed',
        'Awaiting Shipment',
        'Shipped',
        'Delivered', // Delivered bisa dianggap uncompleted sampai is_completed true
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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filterStatus = $request->query('status');

        $ordersQuery = Order::query()->with(['user', 'address', 'orderItems.product'])->whereIn('status', self::UNCOMPLETED_STATUSES)->where('is_completed', false);

        if($filterStatus && in_array($filterStatus, self::ALL_STATUSES)) {
            $ordersQuery->where('status', $filterStatus);
        }

        $orders = $ordersQuery->latest()->paginate(10);

        $allStatuses = self::ALL_STATUSES;

        return view('admin.orders.index', compact('orders', 'allStatuses', 'filterStatus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'address', 'orderItems.product', 'orderItems.productVariant']);
        $allStatuses = self::ALL_STATUSES;

        return view('admin.orders.show', compact('order', 'allStatuses'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', self::ALL_STATUSES)],
        ]);

        try {
            $oldStatus = $order->status;
            $newStatus = $request->input('status');

            // 1. Cek Status Tidak Bisa Dikembalikan
            $oldStatusOrder = self::STATUS_ORDER_MAP[$oldStatus] ?? null;
            $newStatusOrder = self::STATUS_ORDER_MAP[$newStatus] ?? null;

            if (
                $oldStatusOrder !== null && $newStatusOrder !== null &&
                $newStatusOrder < $oldStatusOrder // Jika status baru lebih rendah dari status lama
            ) {
                return redirect()->back()->withErrors(['status' => 'Status tidak bisa dikembalikan ke tahap sebelumnya.']);
            }

            // 2. Logic Khusus untuk 'Order Confirmed' -> 'Awaiting Shipment' (Input Kurir/Resi & Generate PDF Sticker)
            if ($oldStatus === 'Order Confirmed' && $newStatus === 'Awaiting Shipment') {
                $request->validate([
                    'courier_name' => ['required', 'string', 'max:255'],
                    'tracking_number' => ['required', 'string', 'max:255'],
                ]);

                // Validasi courier_name dan tracking_number sudah dilakukan oleh required_if di atas.
                $courierName = $request->input('courier_name');
                $trackingNumber = $request->input('tracking_number');

                // Pastikan alamat pengiriman ada
                if (empty($order->shipping_recipient_name) || empty($order->shipping_address)) {
                    return redirect()->back()->withErrors(['status' => 'Data pengiriman tidak lengkap untuk membuat label.']);
                }

                // Buat entri baru di tabel deliveries
                $delivery = Delivery::create([
                    'order_id' => $order->id,
                    'courier_name' => $courierName,
                    'tracking_number' => $trackingNumber,
                    'shipped_at' => null, // shipped_at masih null di tahap Awaiting Shipment
                    'delivered_at' => null,
                ]);

                // Generate PDF
                $pdf = Pdf::loadView('admin.orders.shipping_label_pdf', compact('order'))->setOptions([
                    'orientation' => 'landscape',
                    'defaultFont' => 'sans-serif',
                ]);
                $fileName = 'shipping_label_' . $order->id . '_' . \Illuminate\Support\Str::random(8) . '.pdf';
                $filePath = 'shipping_labels/' . $fileName;

                Storage::disk('public')->put($filePath, $pdf->output());

                $order->shipping_label = $filePath; // Simpan path label ke database order
                $order->status = $newStatus;
                $order->save();


                // Memberikan file PDF sebagai download
                return Storage::disk('public')->download($filePath, $fileName);
            }

            // 3. Logic Khusus untuk 'Awaiting Shipment' -> 'Shipped' (Hanya Konfirmasi, Set shipped_at)
            if ($oldStatus === 'Awaiting Shipment' && $newStatus === 'Shipped') {
                // Pastikan ada record Delivery yang terkait dan belum shipped_at
                if (!$order->delivery || $order->delivery->shipped_at !== null) {
                    return redirect()->back()->withErrors(['status' => 'Informasi pengiriman tidak ditemukan atau sudah shipped.']);
                }

                $order->delivery->shipped_at = now(); // Set shipped_at saat status menjadi Shipped
                $order->delivery->save(); // Simpan perubahan di model Delivery

                $order->status = $newStatus;
                $order->save();

                return redirect()->route('admin.orders.show', $order)->with('success', 'Status pesanan berhasil diperbarui ke Shipped.');
            }

            // 4. Logic Khusus untuk 'Shipped' -> 'Delivered' (Update delivered_at di Delivery)
            if ($oldStatus === 'Shipped' && $newStatus === 'Delivered') {
                // Pastikan ada record Delivery yang terkait
                if (!$order->delivery) {
                    return redirect()->back()->withErrors(['status' => 'Tidak dapat memperbarui ke Delivered: Informasi pengiriman tidak ditemukan.']);
                }
                $order->delivery->delivered_at = now(); // Set delivered_at
                $order->delivery->save(); // Simpan perubahan di model Delivery

                $order->status = $newStatus;
                $order->auto_complete_at = now()->addDays(2); // Contoh: 2 hari setelah Delivered
                $order->save();

                return redirect()->route('admin.orders.show', $order)->with('success', 'Status pesanan berhasil diperbarui ke Delivered.');
            }


            // 5. Logic Umum untuk Perubahan Status Lainnya (misal Canceled, Returned, Refunded, Completed, Awaiting Payment)
            $order->status = $newStatus;

            // Logika tambahan jika status menjadi 'Completed'
            if ($newStatus === 'Completed') {
                $order->is_completed = true;
                $order->completed_at = now();
            }

            $order->save();

            return redirect()->route('admin.orders.show', $order)->with('success', 'Status pesanan berhasil diperbarui.');

        } catch (\Exception $e) {
            // Log error lengkap untuk debugging
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui status pesanan. Pesan: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function downloadShippingLabel(Order $order)
    {
        // 1. Periksa apakah pesanan memiliki path label pengiriman
        if (!$order->shipping_label) {
            return redirect()->back()->with('error', 'Label pengiriman tidak ditemukan untuk pesanan ini.');
        }

        // 2. Periksa apakah file label pengiriman ada di storage server
        if (!Storage::disk('public')->exists($order->shipping_label)) {
            return redirect()->back()->with('error', 'File label pengiriman tidak ditemukan di server. Harap hubungi administrator.');
        }

        // 3. Tentukan nama file yang akan diunduh oleh browser
        // Nama file yang lebih user-friendly saat diunduh
        $fileName = 'shipping_label_order_' . $order->id . '.pdf';

        // 4. Lakukan unduhan file
        // Storage::disk('public')->download() akan memaksa browser untuk mengunduh file
        return Storage::disk('public')->download($order->shipping_label, $fileName);
    }
}
