<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyTransaction;
use App\Services\Frontend\FrontendPageService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function __construct(private readonly FrontendPageService $pageService)
    {
    }

    public function index(): View
    {
        $customer = auth('customer')->user();
        abort_if(! $customer, 403);

        $balanceRow = LoyaltyTransaction::query()
            ->select(
                'customer_id',
                DB::raw("SUM(CASE WHEN type IN ('credit','adjustment') THEN points ELSE 0 END) - SUM(CASE WHEN type = 'debit' THEN points ELSE 0 END) as balance")
            )
            ->where('customer_id', $customer->id)
            ->groupBy('customer_id')
            ->first();

        return view('frontend.loyalty.index', [
            ...$this->pageService->siteData(),
            'customer' => $customer,
            'balance' => (int) ($balanceRow->balance ?? 0),
            'transactions' => LoyaltyTransaction::query()
                ->where('customer_id', $customer->id)
                ->latest()
                ->paginate(12),
        ]);
    }
}
