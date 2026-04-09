<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpsertSubcategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use App\Services\Backend\SubcategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubcategoryController extends Controller
{
    public function __construct(private readonly SubcategoryService $service)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $status = trim((string) $request->string('status'));
        $categoryId = $request->integer('category_id');

        return view('backend.subcategories.index', [
            'subcategories' => Subcategory::query()
                ->with('category')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%")
                            ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($categoryId > 0, fn ($query) => $query->where('category_id', $categoryId))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'categories' => Category::query()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function show(Subcategory $subcategory): View
    {
        return view('backend.subcategories.show', [
            'subcategory' => $subcategory->load('category.restaurant'),
        ]);
    }

    public function create(): View
    {
        return view('backend.subcategories.form', [
            'subcategory' => new Subcategory(),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(UpsertSubcategoryRequest $request): RedirectResponse
    {
        $this->service->upsert($request->validated());

        return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory created.');
    }

    public function edit(Subcategory $subcategory): View
    {
        return view('backend.subcategories.form', [
            'subcategory' => $subcategory,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpsertSubcategoryRequest $request, Subcategory $subcategory): RedirectResponse
    {
        $this->service->upsert($request->validated(), $subcategory);

        return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory updated.');
    }

    public function toggleStatus(Subcategory $subcategory): RedirectResponse
    {
        $current = $subcategory->status?->value ?? (string) $subcategory->status;
        $subcategory->update([
            'status' => $current === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Subcategory status updated.');
    }

    public function destroy(Subcategory $subcategory): RedirectResponse
    {
        $subcategory->update(['status' => 'inactive']);

        return back()->with('success', 'Subcategory archived.');
    }
}

