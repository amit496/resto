<?php

namespace App\Services\Backend;

use App\Models\Category;
use App\Support\FoodImageResolver;
use App\Traits\HandlesImageUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CategoryService
{
    use HandlesImageUploads;

    public function upsert(array $data, ?Category $category = null): Category
    {
        /** @var UploadedFile|null $newImage */
        $newImage = Arr::pull($data, 'image');
        $removeImage = (bool) Arr::pull($data, 'remove_image', false);
        Arr::pull($data, 'redirect_page');

        $category ??= new Category();
        $category->fill($data);
        $category->slug = $data['slug'] ?: Str::slug($data['name']);

        if ($removeImage && $category->image) {
            $this->deleteImageAndThumb($category->image);
            $category->image = null;
        }

        if ($newImage instanceof UploadedFile) {
            if ($category->image) {
                $this->deleteImageAndThumb($category->image);
            }

            $upload = $this->uploadCompressedImage($newImage, 'category');
            $category->image = $upload['path'];
        }

        if (! $category->image) {
            $category->image = FoodImageResolver::category($category->name);
        }

        $category->save();

        return $category;
    }
}

