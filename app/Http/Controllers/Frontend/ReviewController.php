<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BranchReview;
use App\Models\FoodReview;
use App\Services\Frontend\FrontendPageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(private readonly FrontendPageService $service)
    {
    }

    public function __invoke(): View
    {
        return view('frontend.reviews', $this->service->reviewData());
    }

    public function storeFood(Request $request): RedirectResponse
    {
        $customer = auth('customer')->user();
        abort_if(! $customer, 403);

        $data = $request->validate([
            'phone' => [$customer->phone ? 'nullable' : 'required', 'string', 'max:30'],
            'product_id' => ['required', 'exists:products,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! $customer->phone && ! empty($data['phone'])) {
            $customer->update(['phone' => $data['phone']]);
        }

        FoodReview::query()->create([
            'product_id' => $data['product_id'],
            'branch_id' => $data['branch_id'] ?? null,
            'customer_id' => $customer->id,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'comment' => $data['comment'] ?? null,
            'status' => 'pending',
            'is_published' => false,
        ]);

        return back()->with('success', 'Food review submitted for moderation.');
    }

    public function storeBranch(Request $request): RedirectResponse
    {
        $customer = auth('customer')->user();
        abort_if(! $customer, 403);

        $data = $request->validate([
            'phone' => [$customer->phone ? 'nullable' : 'required', 'string', 'max:30'],
            'branch_id' => ['required', 'exists:branches,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! $customer->phone && ! empty($data['phone'])) {
            $customer->update(['phone' => $data['phone']]);
        }

        BranchReview::query()->create([
            'branch_id' => $data['branch_id'],
            'customer_id' => $customer->id,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'comment' => $data['comment'] ?? null,
            'status' => 'pending',
            'is_published' => false,
        ]);

        return back()->with('success', 'Branch review submitted for moderation.');
    }
}

