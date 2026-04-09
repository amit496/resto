<?php

namespace App\Services\Frontend;

use App\Enums\ProductTypeEnum;
use App\Models\AppSetting;
use App\Models\Branch;
use App\Models\BranchReview;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\FoodReview;
use App\Models\FoodOrderItem;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\Subcategory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FrontendPageService
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function siteData(): array
    {
        $setting = $this->hasTable('app_settings')
            ? (AppSetting::query()->whereNull('restaurant_id')->first() ?? AppSetting::query()->first())
            : null;
        $restaurant = $this->hasTable('restaurants')
            ? Restaurant::query()->withCount(['branches', 'products', 'customers'])->oldest('id')->first()
            : null;

        return [
            'setting' => $setting,
            'restaurant' => $restaurant,
            'cartSummary' => $this->cartService->summary($setting),
            'navigationCategories' => $this->hasTable('categories')
                ? Category::query()
                    ->where('status', 'active')
                    ->orderBy('id')
                    ->take(6)
                    ->get(['id', 'name', 'slug'])
                : collect(),
        ];
    }

    public function homeData(): array
    {
        $site = $this->siteData();

        return [
            ...$site,
            'featuredProducts' => $this->hasTable('products')
                ? Product::query()->with(['category', 'subcategory'])->where('status', 'active')->latest('id')->take(8)->get()
                : collect(),
            'featuredBranches' => $this->hasTable('branches')
                ? Branch::query()->where('status', 'active')->latest('id')->take(6)->get()
                : collect(),
            'activeOffers' => $this->hasTable('coupons')
                ? Coupon::query()->where('status', 'active')->orderByDesc('value')->take(6)->get()
                : collect(),
            'foodReviews' => $this->hasTable('food_reviews')
                ? FoodReview::query()->with(['product', 'customer', 'branch'])->where('status', 'approved')->where('is_published', true)->latest('id')->take(6)->get()
                : collect(),
            'categorySections' => $this->categorySections(),
            'topSellingProducts' => $this->topSellingProducts(),
            'topSellingDrinks' => $this->topSellingProducts(ProductTypeEnum::BEVERAGE->value, 4),
            'stats' => [
                'products' => $this->hasTable('products') ? Product::query()->where('status', 'active')->count() : 0,
                'branches' => $this->hasTable('branches') ? Branch::query()->where('status', 'active')->count() : 0,
                'offers' => $this->hasTable('coupons') ? Coupon::query()->where('status', 'active')->count() : 0,
                'reviews' => $this->hasTable('food_reviews') ? FoodReview::query()->where('status', 'approved')->where('is_published', true)->count() : 0,
            ],
        ];
    }

    public function aboutData(): array
    {
        $site = $this->siteData();

        return [
            ...$site,
            'branches' => $this->hasTable('branches')
                ? Branch::query()->where('status', 'active')->orderBy('name')->get()
                : collect(),
            'branchReviews' => $this->hasTable('branch_reviews')
                ? BranchReview::query()->with(['branch', 'customer'])->where('status', 'approved')->where('is_published', true)->latest('id')->take(6)->get()
                : collect(),
        ];
    }

    public function menuData(
        array $categoryIds = [],
        array $subcategoryIds = [],
        array $types = [],
        string $search = '',
        string $sort = 'latest',
        string $searchIn = 'all',
        ?float $minPrice = null,
        ?float $maxPrice = null
    ): array
    {
        $site = $this->siteData();

        $products = $this->hasTable('products')
            ? Product::query()
                ->with(['category', 'subcategory'])
                ->where('status', 'active')
                ->when($categoryIds !== [], fn ($query) => $query->whereIn('category_id', $categoryIds))
                ->when($subcategoryIds !== [], fn ($query) => $query->whereIn('subcategory_id', $subcategoryIds))
                ->when($types !== [], fn ($query) => $query->whereIn('type', $types))
                ->when($search !== '', function ($query) use ($search, $searchIn) {
                    $query->where(function ($builder) use ($search, $searchIn) {
                        match ($searchIn) {
                            'name' => $builder->where('name', 'like', "%{$search}%"),
                            'description' => $builder->where('description', 'like', "%{$search}%"),
                            'category' => $builder->whereHas('category', fn ($category) => $category->where('name', 'like', "%{$search}%")),
                            'subcategory' => $builder->whereHas('subcategory', fn ($subcategory) => $subcategory->where('name', 'like', "%{$search}%")),
                            'type' => $builder->where('type', 'like', "%{$search}%"),
                            default => $builder
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%")
                                ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('subcategory', fn ($subcategory) => $subcategory->where('name', 'like', "%{$search}%"))
                                ->orWhere('type', 'like', "%{$search}%"),
                        };
                    });
                })
                ->when($minPrice !== null, fn ($query) => $query->where('base_price', '>=', $minPrice))
                ->when($maxPrice !== null, fn ($query) => $query->where('base_price', '<=', $maxPrice))
                ->when($sort === 'price_low', fn ($query) => $query->orderBy('base_price'))
                ->when($sort === 'price_high', fn ($query) => $query->orderByDesc('base_price'))
                ->when($sort === 'name', fn ($query) => $query->orderBy('name'))
                ->when($sort === 'popular', fn ($query) => $query->orderByDesc('id'))
                ->when(! in_array($sort, ['price_low', 'price_high', 'name', 'popular'], true), fn ($query) => $query->latest('id'))
                ->paginate(12)
                ->withQueryString()
            : $this->emptyPaginator();

        return [
            ...$site,
            'products' => $products,
            'categories' => $this->hasTable('categories')
                ? Category::query()->where('status', 'active')->orderBy('id')->get()
                : collect(),
            'subcategories' => $this->hasTable('subcategories')
                ? Subcategory::query()
                    ->where('status', 'active')
                    ->with('category:id,name,slug')
                    ->orderBy('id')
                    ->get()
                : collect(),
            'selectedCategoryIds' => $categoryIds,
            'selectedSubcategoryIds' => $subcategoryIds,
            'selectedTypes' => $types,
            'selectedSort' => $sort,
            'selectedSearchIn' => $searchIn,
            'selectedMinPrice' => $minPrice,
            'selectedMaxPrice' => $maxPrice,
        ];
    }

    public function menuItemData(Product $product): array
    {
        return [
            ...$this->siteData(),
            'product' => $product->load(['category', 'subcategory', 'variants', 'reviews.customer', 'reviews.branch']),
            'relatedProducts' => $this->hasTable('products')
                ? Product::query()->where('status', 'active')->where('id', '!=', $product->id)->where('category_id', $product->category_id)->latest('id')->take(4)->get()
                : collect(),
        ];
    }

    public function branchData(): array
    {
        return [
            ...$this->siteData(),
            'branches' => $this->hasTable('branches')
                ? Branch::query()->where('status', 'active')->orderBy('name')->paginate(9)
                : $this->emptyPaginator(),
        ];
    }

    public function branchDetailData(Branch $branch): array
    {
        return [
            ...$this->siteData(),
            'branch' => $branch->load(['restaurant', 'reviews.customer', 'foodReviews.product', 'foodReviews.customer']),
        ];
    }

    public function offerData(): array
    {
        return [
            ...$this->siteData(),
            'offers' => $this->hasTable('coupons')
                ? Coupon::query()->where('status', 'active')->orderBy('end_date')->paginate(9)
                : $this->emptyPaginator(),
        ];
    }

    public function reviewData(): array
    {
        return [
            ...$this->siteData(),
            'foodReviews' => $this->hasTable('food_reviews')
                ? FoodReview::query()->with(['product', 'customer', 'branch'])->where('status', 'approved')->where('is_published', true)->latest('id')->paginate(9, ['*'], 'food_page')
                : $this->emptyPaginator(pageName: 'food_page'),
            'branchReviews' => $this->hasTable('branch_reviews')
                ? BranchReview::query()->with(['branch', 'customer'])->where('status', 'approved')->where('is_published', true)->latest('id')->paginate(9, ['*'], 'branch_page')
                : $this->emptyPaginator(pageName: 'branch_page'),
            'reviewProducts' => $this->hasTable('products')
                ? Product::query()->where('status', 'active')->orderBy('name')->get(['id', 'name'])
                : collect(),
            'reviewBranches' => $this->hasTable('branches')
                ? Branch::query()->where('status', 'active')->orderBy('name')->get(['id', 'name'])
                : collect(),
        ];
    }

    public function categorySections(int $limitPerCategory = 4)
    {
        if (! $this->hasTable('categories') || ! $this->hasTable('products')) {
            return collect();
        }

        return Category::query()
            ->where('status', 'active')
            ->with(['restaurant', 'subcategories'])
            ->orderBy('id')
            ->get()
            ->map(function (Category $category) use ($limitPerCategory) {
                $products = Product::query()
                    ->where('status', 'active')
                    ->where('category_id', $category->id)
                    ->latest('id')
                    ->take($limitPerCategory)
                    ->get();

                return [
                    'category' => $category,
                    'products' => $products,
                ];
            })
            ->filter(fn (array $row) => $row['products']->isNotEmpty())
            ->values();
    }

    private function hasTable(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function topSellingProducts(?string $type = null, int $limit = 6): Collection
    {
        if (! $this->hasTable('food_order_items') || ! $this->hasTable('products')) {
            return collect();
        }

        $productIds = FoodOrderItem::query()
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(30)
            ->pluck('total_qty', 'product_id');

        if ($productIds->isEmpty()) {
            $fallback = Product::query()
                ->with(['category', 'subcategory'])
                ->where('status', 'active')
                ->when($type, fn ($query) => $query->where('type', $type))
                ->latest('id')
                ->take($limit)
                ->get();

            return $fallback->each(function (Product $product): void {
                $product->setAttribute('total_sold', 0);
            });
        }

        $products = Product::query()
            ->with(['category', 'subcategory'])
            ->whereIn('id', $productIds->keys())
            ->where('status', 'active')
            ->when($type, fn ($query) => $query->where('type', $type))
            ->get()
            ->map(function (Product $product) use ($productIds) {
                $product->setAttribute('total_sold', (int) ($productIds[$product->id] ?? 0));

                return $product;
            })
            ->sortByDesc('total_sold')
            ->take($limit)
            ->values();

        if ($products->count() < $limit) {
            $missing = Product::query()
                ->with(['category', 'subcategory'])
                ->where('status', 'active')
                ->when($type, fn ($query) => $query->where('type', $type))
                ->whereNotIn('id', $products->pluck('id'))
                ->latest('id')
                ->take($limit - $products->count())
                ->get()
                ->each(function (Product $product): void {
                    $product->setAttribute('total_sold', 0);
                });

            return $products->concat($missing)->values();
        }

        return $products;
    }

    private function emptyPaginator(int $perPage = 9, string $pageName = 'page'): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            collect(),
            0,
            $perPage,
            1,
            ['path' => request()->url(), 'pageName' => $pageName]
        );
    }
}

