<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchReview;
use App\Models\Customer;
use App\Models\FoodReview;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $branchId = $request->integer('branch_id');
        $status = trim((string) $request->string('status'));
        $published = trim((string) $request->string('published'));

        return view('backend.reviews.index', [
            'foodReviews' => FoodReview::query()
                ->with(['product', 'branch', 'customer'])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('title', 'like', "%{$search}%")
                            ->orWhere('comment', 'like', "%{$search}%")
                            ->orWhereHas('product', fn ($product) => $product->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($branchId > 0, fn ($query) => $query->where('branch_id', $branchId))
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($published === 'published', fn ($query) => $query->where('is_published', true))
                ->when($published === 'hidden', fn ($query) => $query->where('is_published', false))
                ->latest()
                ->paginate(10, ['*'], 'food_page')
                ->withQueryString(),
            'branchReviews' => BranchReview::query()
                ->with(['branch', 'customer'])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('title', 'like', "%{$search}%")
                            ->orWhere('comment', 'like', "%{$search}%")
                            ->orWhereHas('branch', fn ($branch) => $branch->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($branchId > 0, fn ($query) => $query->where('branch_id', $branchId))
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($published === 'published', fn ($query) => $query->where('is_published', true))
                ->when($published === 'hidden', fn ($query) => $query->where('is_published', false))
                ->latest()
                ->paginate(10, ['*'], 'branch_page')
                ->withQueryString(),
            'branches' => Branch::query()->select('id', 'name')->orderBy('name')->get(),
            'products' => Product::query()->select('id', 'name')->orderBy('name')->get(),
            'customers' => Customer::query()->select('id', 'name')->orderBy('name')->get(),
            'statusOptions' => ['pending', 'approved', 'rejected'],
        ]);
    }

    public function storeFood(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:190'],
            'comment' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        FoodReview::query()->create([
            ...$data,
            'is_published' => (bool) ($data['is_published'] ?? false),
        ]);

        return back()->with('success', 'Food review added.');
    }

    public function storeBranch(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:190'],
            'comment' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        BranchReview::query()->create([
            ...$data,
            'is_published' => (bool) ($data['is_published'] ?? false),
        ]);

        return back()->with('success', 'Branch review added.');
    }

    public function updateFoodStatus(Request $request, FoodReview $foodReview): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $foodReview->update($data);

        return back()->with('success', 'Food review status updated.');
    }

    public function updateBranchStatus(Request $request, BranchReview $branchReview): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $branchReview->update($data);

        return back()->with('success', 'Branch review status updated.');
    }

    public function toggleFoodPublish(FoodReview $foodReview): RedirectResponse
    {
        $foodReview->update([
            'is_published' => ! $foodReview->is_published,
        ]);

        return back()->with('success', 'Food review publish status updated.');
    }

    public function toggleBranchPublish(BranchReview $branchReview): RedirectResponse
    {
        $branchReview->update([
            'is_published' => ! $branchReview->is_published,
        ]);

        return back()->with('success', 'Branch review publish status updated.');
    }

    public function destroyFood(FoodReview $foodReview): RedirectResponse
    {
        $foodReview->delete();

        return back()->with('success', 'Food review deleted.');
    }

    public function destroyBranch(BranchReview $branchReview): RedirectResponse
    {
        $branchReview->delete();

        return back()->with('success', 'Branch review deleted.');
    }
}

