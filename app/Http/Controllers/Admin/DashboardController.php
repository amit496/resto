<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Enums\StaffTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\BranchReview;
use App\Models\Customer;
use App\Models\FoodOrder;
use App\Models\NotificationLog;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        Gate::authorize('dashboard.view');

        $restaurantId = $request->integer('restaurant_id');
        $branchId = $request->integer('branch_id');

        $restaurants = Restaurant::query()->orderBy('name')->get(['id', 'name']);
        $branches = Branch::query()
            ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
            ->orderBy('name')
            ->get();

        $branchIds = $branches->pluck('id');

        $ordersBase = FoodOrder::query()
            ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
            ->when($branchId > 0, fn ($query) => $query->where('branch_id', $branchId));

        $monthStart = now()->subMonths(11)->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $monthlyRows = FoodOrder::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as orders, COALESCE(SUM(total_amount),0) as revenue")
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
            ->when($branchId > 0, fn ($query) => $query->where('branch_id', $branchId))
            ->groupBy('period')
            ->get()
            ->keyBy('period');

        $monthLabels = [];
        $monthlyOrders = [];
        $monthlyRevenue = [];

        for ($i = 0; $i < 12; $i++) {
            $period = Carbon::parse($monthStart)->addMonths($i);
            $key = $period->format('Y-m');
            $monthLabels[] = $period->format('M Y');
            $monthlyOrders[] = (int) ($monthlyRows[$key]->orders ?? 0);
            $monthlyRevenue[] = (float) ($monthlyRows[$key]->revenue ?? 0);
        }

        $dailyStart = now()->subDays(6)->startOfDay();
        $dailyRows = FoodOrder::query()
            ->selectRaw('DATE(created_at) as period, COUNT(*) as orders')
            ->whereBetween('created_at', [$dailyStart, now()])
            ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
            ->when($branchId > 0, fn ($query) => $query->where('branch_id', $branchId))
            ->groupBy('period')
            ->get()
            ->keyBy('period');

        $dailyLabels = [];
        $dailyOrders = [];
        for ($i = 0; $i < 7; $i++) {
            $period = Carbon::parse($dailyStart)->addDays($i);
            $key = $period->format('Y-m-d');
            $dailyLabels[] = $period->format('D');
            $dailyOrders[] = (int) ($dailyRows[$key]->orders ?? 0);
        }

        $ordersByBranch = FoodOrder::query()
            ->select('branch_id', DB::raw('COUNT(*) as orders'), DB::raw('COALESCE(SUM(total_amount),0) as revenue'))
            ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
            ->whereIn('branch_id', $branchIds)
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        $currentMonthStart = now()->startOfMonth();
        $currentYearStart = now()->startOfYear();

        $monthlyByBranch = FoodOrder::query()
            ->select('branch_id', DB::raw('COUNT(*) as orders'), DB::raw('COALESCE(SUM(total_amount),0) as revenue'))
            ->where('created_at', '>=', $currentMonthStart)
            ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
            ->whereIn('branch_id', $branchIds)
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        $yearlyByBranch = FoodOrder::query()
            ->select('branch_id', DB::raw('COUNT(*) as orders'), DB::raw('COALESCE(SUM(total_amount),0) as revenue'))
            ->where('created_at', '>=', $currentYearStart)
            ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
            ->whereIn('branch_id', $branchIds)
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        $ratingsByBranch = BranchReview::query()
            ->select('branch_id', DB::raw('AVG(rating) as avg_rating'), DB::raw('COUNT(*) as reviews'))
            ->where('is_published', true)
            ->whereIn('branch_id', $branchIds)
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        $staffCounts = User::query()
            ->select('branch_id', 'staff_type', DB::raw('COUNT(*) as total'))
            ->whereNotNull('branch_id')
            ->whereIn('branch_id', $branchIds)
            ->whereHas('roles', fn ($query) => $query->whereIn('name', [RoleEnum::MANAGER->value, RoleEnum::STAFF->value]))
            ->groupBy('branch_id', 'staff_type')
            ->get()
            ->groupBy('branch_id');

        $staffTypes = StaffTypeEnum::cases();

        $branchMetrics = $branches->map(function ($branch) use (
            $ordersByBranch,
            $monthlyByBranch,
            $yearlyByBranch,
            $ratingsByBranch,
            $staffCounts,
            $staffTypes
        ) {
            $orders = $ordersByBranch->get($branch->id);
            $month = $monthlyByBranch->get($branch->id);
            $year = $yearlyByBranch->get($branch->id);
            $rating = $ratingsByBranch->get($branch->id);

            $staffBreakdown = collect($staffTypes)->mapWithKeys(function ($type) use ($staffCounts, $branch) {
                $count = $staffCounts->get($branch->id)?->firstWhere('staff_type', $type->value)?->total ?? 0;

                return [$type->label() => (int) $count];
            });

            return [
                'branch' => $branch,
                'orders' => (int) ($orders->orders ?? 0),
                'revenue' => (float) ($orders->revenue ?? 0),
                'avg_rating' => $rating ? round((float) $rating->avg_rating, 1) : 0,
                'reviews' => (int) ($rating->reviews ?? 0),
                'month_orders' => (int) ($month->orders ?? 0),
                'month_revenue' => (float) ($month->revenue ?? 0),
                'year_orders' => (int) ($year->orders ?? 0),
                'year_revenue' => (float) ($year->revenue ?? 0),
                'staff' => $staffBreakdown,
            ];
        });

        $bestBranch = $branchMetrics
            ->sortByDesc(fn ($item) => [$item['avg_rating'], $item['revenue']])
            ->first();

        return view('admin.dashboard', [
            'userCount' => User::query()->count(),
            'adminCount' => User::role('admin')->count(),
            'roleCount' => Role::query()->count(),
            'auditCount' => AuditLog::query()->count(),
            'customerCount' => Customer::query()->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))->count(),
            'branchCount' => $branches->count(),
            'orderCount' => (clone $ordersBase)->count(),
            'revenueTotal' => (float) (clone $ordersBase)->sum('total_amount'),
            'monthlyLabels' => $monthLabels,
            'monthlyOrders' => $monthlyOrders,
            'monthlyRevenue' => $monthlyRevenue,
            'dailyLabels' => $dailyLabels,
            'dailyOrders' => $dailyOrders,
            'branchMetrics' => $branchMetrics,
            'bestBranch' => $bestBranch,
            'restaurants' => $restaurants,
            'branches' => $branches,
            'selectedRestaurant' => $restaurantId,
            'selectedBranch' => $branchId,
            'latestNotifications' => NotificationLog::query()
                ->whereIn('audience', ['admin', 'all'])
                ->latest()
                ->take(6)
                ->get(),
            'latestLogs' => AuditLog::query()
                ->with('user')
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }
}

