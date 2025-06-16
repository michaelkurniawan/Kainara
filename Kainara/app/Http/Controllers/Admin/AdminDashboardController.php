<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\ProductVariant;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $activeOrderStatuses = ['Awaiting Payment', 'Order Confirmed', 'Awaiting Shipment', 'Shipped'];
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $adminName = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $activeOrdersCount = Order::whereIn('status', $activeOrderStatuses)->count();
        $monthlyRevenue = Order::where('status', 'Delivered')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('subtotal');

        $formatedMonthlyRevenue = number_format($monthlyRevenue, 0, '.', ',');

        $totalStock = number_format(ProductVariant::sum('stock'), 0, '.', ',');

        $recentOrders = Order::orderBy('created_at', 'desc')->limit(2)->select(['id', 'original_user_name', 'subtotal', 'status', 'created_at'])->get();

        $todayRevenue = number_format(Order::where('status', 'Delivered')->whereDate('completed_at', now())->sum('subtotal'), 0, '.', ',');
        $weekRevenue = number_format(Order::where('status', 'Delivered')->whereBetween('completed_at', [now()->subDays(7), now()])->sum('subtotal'), 0, '.', ',');
        $allTimeRevenue = number_format(Order::where('status', 'Delivered')->sum('subtotal'), 0, '.', ',');

        return view('admin.dashboard', compact([
            'adminName',
            'activeOrdersCount',
            'formatedMonthlyRevenue',
            'totalStock',
            'recentOrders',
            'todayRevenue',
            'weekRevenue',
            'allTimeRevenue',
        ]));
    }
}
