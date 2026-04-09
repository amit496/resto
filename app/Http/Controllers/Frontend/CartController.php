<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Frontend\CartService;
use App\Services\Frontend\FrontendPageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly FrontendPageService $pageService,
        private readonly CartService $cartService
    ) {
    }

    public function index(): View
    {
        $site = $this->pageService->siteData();
        $customer = auth('customer')->user();

        return view('frontend.cart.index', [
            ...$site,
            'cart' => $this->cartService->summary($site['setting'], $customer),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $product = Product::query()->findOrFail($data['product_id']);
        $this->cartService->add($product, $data['product_variant_id'] ?? null, (int) ($data['quantity'] ?? 1));

        return back()->with('success', 'Item added to cart.');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:60'],
        ]);

        $site = $this->pageService->siteData();
        $summary = $this->cartService->summary($site['setting'], auth('customer')->user());
        $result = $this->cartService->applyCoupon($data['code'], (float) $summary['subtotal']);

        if (! $result['coupon']) {
            return back()->with('error', $result['error'] ?? 'Unable to apply coupon.');
        }

        return back()->with('success', 'Coupon applied.');
    }

    public function clearCoupon(): RedirectResponse
    {
        $this->cartService->clearCoupon();

        return back()->with('success', 'Coupon removed.');
    }

    public function updateLoyalty(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'loyalty_points' => ['required', 'integer', 'min:0'],
        ]);

        $this->cartService->setLoyaltyPoints((int) $data['loyalty_points']);

        return back()->with('success', 'Loyalty points updated.');
    }

    public function update(Request $request, string $itemKey): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:0', 'max:20'],
            'delta' => ['nullable', 'integer', 'in:-1,1'],
        ]);

        $quantity = array_key_exists('quantity', $data) && $data['quantity'] !== null
            ? (int) $data['quantity']
            : null;

        if (array_key_exists('delta', $data) && $data['delta'] !== null) {
            $currentItem = $this->cartService->items()->firstWhere('key', $itemKey);
            $currentQuantity = (int) ($currentItem['quantity'] ?? 0);
            $quantity = $currentQuantity + (int) $data['delta'];
        }

        if ($quantity === null) {
            return back()->with('error', 'Quantity update failed.');
        }

        $this->cartService->update($itemKey, $quantity);

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(string $itemKey): RedirectResponse
    {
        $this->cartService->remove($itemKey);

        return back()->with('success', 'Item removed from cart.');
    }
}

