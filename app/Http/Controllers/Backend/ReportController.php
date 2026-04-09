<?php

namespace App\Http\Controllers\Backend;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\DeliveryBoy;
use App\Models\FoodOrder;
use App\Models\FoodOrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $report = $this->buildReport($request);

        return view('backend.reports.index', $report);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $report = $this->buildReport($request);
        $filename = 'report-'.$report['from']->toDateString().'_to_'.$report['to']->toDateString().'.csv';

        return response()->streamDownload(function () use ($report) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Metric', 'Value']);
            fputcsv($handle, ['Orders', $report['ordersCount']]);
            fputcsv($handle, ['Delivered', $report['deliveredOrdersCount']]);
            fputcsv($handle, ['Cancelled', $report['cancelledOrdersCount']]);
            fputcsv($handle, ['Revenue', $report['revenue']]);
            fputcsv($handle, ['Avg Order Value', $report['avgOrderValue']]);
            fputcsv($handle, ['Order Success %', $report['orderSuccessRate']]);
            fputcsv($handle, ['Cancellation %', $report['cancellationRate']]);
            fputcsv($handle, ['Payments', $report['paymentsCount']]);
            fputcsv($handle, ['Customers', $report['customersCount']]);
            fputcsv($handle, ['Products', $report['productsCount']]);
            fputcsv($handle, ['Delivery Boys', $report['deliveryBoysCount']]);
            fputcsv($handle, ['Coupons', $report['couponsCount']]);

            fputcsv($handle, []);
            fputcsv($handle, ['Orders By Status']);
            fputcsv($handle, ['Status', 'Orders']);
            foreach ($report['ordersByStatus'] as $row) {
                $statusValue = $row->status instanceof \BackedEnum ? $row->status->value : $row->status;
                fputcsv($handle, [$statusValue, $row->total]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Payments By Method']);
            fputcsv($handle, ['Method', 'Transactions', 'Paid Amount']);
            foreach ($report['paymentsByMethod'] as $row) {
                $methodValue = $row->method instanceof \BackedEnum ? $row->method->value : $row->method;
                fputcsv($handle, [$methodValue, $row->total_transactions, $row->paid_amount]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Top Selling Items']);
            fputcsv($handle, ['Item', 'Qty', 'Sales']);
            foreach ($report['topProducts'] as $row) {
                fputcsv($handle, [$row->name, $row->total_qty, $row->total_sales]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Top Selling Restaurants']);
            fputcsv($handle, ['Restaurant', 'Sales']);
            foreach ($report['topRestaurants'] as $row) {
                fputcsv($handle, [$row->name, $row->total_sales]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Top Selling Branches']);
            fputcsv($handle, ['Branch', 'Sales']);
            foreach ($report['topBranches'] as $row) {
                fputcsv($handle, [$row->name, $row->total_sales]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPdf(Request $request): StreamedResponse
    {
        $report = $this->buildReport($request);
        $pdf = Pdf::loadView('backend.reports.pdf', $report)->setPaper('a4', 'portrait');
        $filename = 'report-'.$report['from']->toDateString().'_to_'.$report['to']->toDateString().'.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function buildReport(Request $request): array
    {
        $from = $request->date('from')?->startOfDay() ?? now()->subDays(29)->startOfDay();
        $to = $request->date('to')?->endOfDay() ?? now()->endOfDay();
        $branchId = $request->integer('branch_id');
        $search = trim((string) $request->string('q'));

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $ordersQuery = FoodOrder::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('order_no', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($branchId > 0, fn ($query) => $query->where('branch_id', $branchId));
        $paymentsQuery = Payment::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('transaction_ref', 'like', "%{$search}%")
                        ->orWhereHas('order', fn ($order) => $order->where('order_no', 'like', "%{$search}%"));
                });
            })
            ->when($branchId > 0, fn ($query) => $query->whereHas('order', fn ($order) => $order->where('branch_id', $branchId)));

        $ordersCount = (clone $ordersQuery)->count();
        $cancelledOrdersCount = (clone $ordersQuery)->where('status', 'cancelled')->count();
        $deliveredOrdersCount = (clone $ordersQuery)->where('status', 'delivered')->count();
        $revenue = (clone $paymentsQuery)->where('status', 'paid')->sum('amount');
        $avgOrderValue = $ordersCount > 0 ? round((float) $revenue / $ordersCount, 2) : 0.0;
        $orderSuccessRate = $ordersCount > 0 ? round(($deliveredOrdersCount / $ordersCount) * 100, 2) : 0.0;
        $cancellationRate = $ordersCount > 0 ? round(($cancelledOrdersCount / $ordersCount) * 100, 2) : 0.0;

        $ordersByStatus = (clone $ordersQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $paymentsByMethod = (clone $paymentsQuery)
            ->select(
                'method',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw("SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as paid_amount")
            )
            ->groupBy('method')
            ->orderByDesc('paid_amount')
            ->get();

        $topProducts = FoodOrderItem::query()
            ->join('food_orders', 'food_orders.id', '=', 'food_order_items.food_order_id')
            ->join('products', 'products.id', '=', 'food_order_items.product_id')
            ->whereBetween('food_orders.created_at', [$from, $to])
            ->when($search !== '', fn ($query) => $query->where('products.name', 'like', "%{$search}%"))
            ->when($branchId > 0, fn ($query) => $query->where('food_orders.branch_id', $branchId))
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(food_order_items.quantity) as total_qty'),
                DB::raw('SUM(food_order_items.line_total) as total_sales')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(8)
            ->get();

        $topRestaurants = Payment::query()
            ->join('food_orders', 'food_orders.id', '=', 'payments.food_order_id')
            ->join('restaurants', 'restaurants.id', '=', 'food_orders.restaurant_id')
            ->whereBetween('payments.created_at', [$from, $to])
            ->where('payments.status', 'paid')
            ->select('restaurants.id', 'restaurants.name', DB::raw('SUM(payments.amount) as total_sales'))
            ->groupBy('restaurants.id', 'restaurants.name')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        $topBranches = Payment::query()
            ->join('food_orders', 'food_orders.id', '=', 'payments.food_order_id')
            ->leftJoin('branches', 'branches.id', '=', 'food_orders.branch_id')
            ->whereBetween('payments.created_at', [$from, $to])
            ->where('payments.status', 'paid')
            ->select(
                DB::raw("COALESCE(branches.name, 'Unassigned') as name"),
                DB::raw('SUM(payments.amount) as total_sales')
            )
            ->groupBy('name')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        return [
            'from' => $from,
            'to' => $to,
            'branchId' => $branchId,
            'search' => $search,
            'branches' => Branch::query()->select('id', 'name')->orderBy('name')->get(),
            'ordersCount' => $ordersCount,
            'deliveredOrdersCount' => $deliveredOrdersCount,
            'cancelledOrdersCount' => $cancelledOrdersCount,
            'customersCount' => Customer::query()->count(),
            'productsCount' => Product::query()->count(),
            'deliveryBoysCount' => DeliveryBoy::query()->count(),
            'couponsCount' => Coupon::query()->count(),
            'paymentsCount' => (clone $paymentsQuery)->count(),
            'revenue' => $revenue,
            'avgOrderValue' => $avgOrderValue,
            'orderSuccessRate' => $orderSuccessRate,
            'cancellationRate' => $cancellationRate,
            'ordersByStatus' => $ordersByStatus,
            'paymentsByMethod' => $paymentsByMethod,
            'topProducts' => $topProducts,
            'topRestaurants' => $topRestaurants,
            'topBranches' => $topBranches,
        ];
    }
}

