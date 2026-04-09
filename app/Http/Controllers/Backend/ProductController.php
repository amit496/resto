<?php

namespace App\Http\Controllers\Backend;

use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpsertProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\Subcategory;
use App\Services\Backend\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $service)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $status = trim((string) $request->string('status'));
        $type = trim((string) $request->string('type'));
        $categoryId = $request->integer('category_id');
        $restaurantId = $request->integer('restaurant_id');

        return view('backend.products.index', [
            'products' => Product::query()
                ->with(['restaurant', 'category', 'subcategory', 'variants'])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%")
                            ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('subcategory', fn ($subcategory) => $subcategory->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($type !== '', fn ($query) => $query->where('type', $type))
                ->when($categoryId > 0, fn ($query) => $query->where('category_id', $categoryId))
                ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'categories' => Category::query()->select('id', 'name')->orderBy('name')->get(),
            'restaurants' => Restaurant::query()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function show(Product $product): View
    {
        return view('backend.products.show', [
            'product' => $product->load(['restaurant', 'category', 'subcategory', 'variants']),
        ]);
    }

    public function create(): View
    {
        return view('backend.products.form', [
            'product' => new Product(),
            'restaurants' => Restaurant::query()->orderBy('name')->get(),
            'categories' => Category::query()->orderBy('name')->get(),
            'subcategories' => Subcategory::query()->orderBy('name')->get(),
            'typeOptions' => ProductTypeEnum::cases(),
            'statusOptions' => ProductStatusEnum::cases(),
        ]);
    }

    public function store(UpsertProductRequest $request): RedirectResponse
    {
        $this->service->upsert($request->validated());

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('backend.products.form', [
            'product' => $product->load('variants'),
            'restaurants' => Restaurant::query()->orderBy('name')->get(),
            'categories' => Category::query()->orderBy('name')->get(),
            'subcategories' => Subcategory::query()->orderBy('name')->get(),
            'typeOptions' => ProductTypeEnum::cases(),
            'statusOptions' => ProductStatusEnum::cases(),
        ]);
    }

    public function update(UpsertProductRequest $request, Product $product): RedirectResponse
    {
        $this->service->upsert($request->validated(), $product);

        return redirect()
            ->route('admin.products.index', ['page' => $request->integer('redirect_page', 1)])
            ->with('success', 'Product updated.');
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $current = $product->status?->value ?? (string) $product->status;
        $product->update([
            'status' => $current === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Product status updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->update(['status' => 'inactive']);

        return back()->with('success', 'Product archived.');
    }
}

