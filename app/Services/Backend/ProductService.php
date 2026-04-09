<?php

namespace App\Services\Backend;

use App\Models\Product;
use App\Support\FoodImageResolver;
use App\Traits\HandlesImageUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProductService
{
    use HandlesImageUploads;

    public function upsert(array $data, ?Product $product = null): Product
    {
        $variants = Arr::pull($data, 'variants', []);
        /** @var UploadedFile[] $newImages */
        $newImages = Arr::pull($data, 'product_images', []);
        $removeImages = Arr::pull($data, 'remove_images', []);
        Arr::pull($data, 'redirect_page');

        $product ??= new Product();
        $product->fill($data);
        $product->slug = $data['slug'] ?: Str::slug($data['name']);
        $product->has_variants = ! empty($variants);

        $existingImages = $product->images ?? [];
        $keptImages = array_values(array_filter(
            $existingImages,
            fn (string $path): bool => ! in_array($path, $removeImages, true)
        ));

        foreach (array_diff($existingImages, $keptImages) as $pathToDelete) {
            $this->deleteImageAndThumb($pathToDelete);
        }

        $uploadedPaths = [];
        foreach ($newImages as $image) {
            if ($image instanceof UploadedFile) {
                $uploadedPaths[] = $this->uploadCompressedImage($image, 'food-items')['path'];
            }
        }

        $product->images = array_values(array_merge($keptImages, $uploadedPaths));
        if (empty($product->images)) {
            $baseSlug = Str::slug($product->name ?: 'food');
            $product->images = [
                FoodImageResolver::product($baseSlug, 1),
                FoodImageResolver::product($baseSlug, 2),
                FoodImageResolver::product($baseSlug, 3),
            ];
        }
        $product->save();

        $keepVariantIds = [];
        foreach ($variants as $variantData) {
            $variant = $product->variants()->updateOrCreate(
                ['id' => $variantData['id'] ?? null],
                [
                    'name' => $variantData['name'],
                    'value' => $variantData['value'] ?? null,
                    'price' => $variantData['price'],
                    'sku' => $variantData['sku'] ?? null,
                    'is_default' => (bool) ($variantData['is_default'] ?? false),
                    'status' => $variantData['status'] ?? 'active',
                ]
            );
            $keepVariantIds[] = $variant->id;
        }

        if (! empty($keepVariantIds)) {
            $product->variants()->whereNotIn('id', $keepVariantIds)->delete();
        } else {
            $product->variants()->delete();
        }

        return $product->fresh('variants');
    }
}

