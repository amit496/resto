<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpsertCategoryRequest;
use App\Models\Category;
use App\Models\Restaurant;
use App\Services\Backend\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $service)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $status = trim((string) $request->string('status'));
        $restaurantId = $request->integer('restaurant_id');

        return view('backend.categories.index', [
            'categories' => Category::query()
                ->with('restaurant')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%")
                            ->orWhereHas('restaurant', fn ($restaurant) => $restaurant->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'restaurants' => Restaurant::query()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function show(Category $category): View
    {
        return view('backend.categories.show', [
            'category' => $category->load(['restaurant', 'subcategories']),
        ]);
    }

    public function create(): View
    {
        return view('backend.categories.form', [
            'category' => new Category(),
            'restaurants' => Restaurant::query()->orderBy('name')->get(),
        ]);
    }

    public function store(UpsertCategoryRequest $request): RedirectResponse
    {
        $this->service->upsert($request->validated());

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('backend.categories.form', [
            'category' => $category,
            'restaurants' => Restaurant::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpsertCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->service->upsert($request->validated(), $category);

        return redirect()
            ->route('admin.categories.index', ['page' => $request->integer('redirect_page', 1)])
            ->with('success', 'Category updated.');
    }

    public function toggleStatus(Category $category): RedirectResponse
    {
        $current = $category->status?->value ?? (string) $category->status;
        $category->update([
            'status' => $current === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Category status updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->update(['status' => 'inactive']);

        return back()->with('success', 'Category archived.');
    }
}

