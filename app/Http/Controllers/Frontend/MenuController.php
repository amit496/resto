<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Services\Frontend\FrontendPageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __construct(private readonly FrontendPageService $service)
    {
    }

    public function index(Request $request, ?Category $category = null, ?Subcategory $subcategory = null): View
    {
        if ($subcategory && $category && $subcategory->category_id !== $category->id) {
            abort(404);
        }

        $data = $this->service->menuData(...$this->filters($request, $category, $subcategory));

        return view('frontend.menu.index', array_merge($data, [
            'currentCategory' => $category,
            'currentSubcategory' => $subcategory,
        ]));
    }

    public function feed(Request $request): JsonResponse
    {
        $category = null;
        $subcategory = null;

        if ($request->filled('category_slug')) {
            $category = Category::query()->where('slug', $request->string('category_slug'))->first();
        }

        if ($request->filled('subcategory_slug')) {
            $subcategory = Subcategory::query()->where('slug', $request->string('subcategory_slug'))->first();
        }

        $data = $this->service->menuData(...$this->filters($request, $category, $subcategory));

        $html = view('frontend.menu.partials.items', [
            'products' => $data['products'],
            'setting' => $data['setting'],
        ])->render();

        return response()->json([
            'html' => $html,
            'next_page' => $data['products']->nextPageUrl(),
        ]);
    }

    public function show(Product $product): View
    {
        return view('frontend.menu.show', $this->service->menuItemData($product));
    }

    private function filters(Request $request, ?Category $category = null, ?Subcategory $subcategory = null): array
    {
        $categoryIds = array_filter(array_map('intval', (array) $request->input('categories', [])));
        $subcategoryIds = array_filter(array_map('intval', (array) $request->input('subcategories', [])));

        if ($category) {
            array_unshift($categoryIds, $category->id);
        }

        if ($subcategory) {
            array_unshift($subcategoryIds, $subcategory->id);
            if (! $category) {
                array_unshift($categoryIds, $subcategory->category_id);
            }
        }

        return [
            array_values(array_unique($categoryIds)),
            array_values(array_unique($subcategoryIds)),
            array_filter((array) $request->input('types', [])),
            trim((string) $request->string('q')),
            trim((string) $request->string('sort', 'latest')),
            trim((string) $request->string('search_in', 'all')),
            $request->filled('min_price') ? (float) $request->input('min_price') : null,
            $request->filled('max_price') ? (float) $request->input('max_price') : null,
        ];
    }
}

