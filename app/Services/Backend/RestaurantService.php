<?php

namespace App\Services\Backend;

use App\Models\Restaurant;
use App\Traits\HandlesImageUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RestaurantService
{
    use HandlesImageUploads;

    public function upsert(array $data, ?Restaurant $restaurant = null): Restaurant
    {
        /** @var UploadedFile|null $logo */
        $logo = Arr::pull($data, 'logo');
        /** @var UploadedFile|null $coverImage */
        $coverImage = Arr::pull($data, 'cover_image_file');
        $removeLogo = (bool) Arr::pull($data, 'remove_logo', false);
        $removeCoverImage = (bool) Arr::pull($data, 'remove_cover_image', false);

        $restaurant ??= new Restaurant();
        $restaurant->fill($data);
        $restaurant->slug = $data['slug'] ?: Str::slug($data['name']);

        if ($removeLogo && $restaurant->logo_path) {
            $this->deleteImageAndThumb($restaurant->logo_path);
            $restaurant->logo_path = null;
        }

        if ($removeCoverImage && $restaurant->cover_image) {
            $this->deleteImageAndThumb($restaurant->cover_image);
            $restaurant->cover_image = null;
        }

        if ($logo instanceof UploadedFile) {
            if ($restaurant->logo_path) {
                $this->deleteImageAndThumb($restaurant->logo_path);
            }
            $restaurant->logo_path = $this->uploadCompressedImage($logo, 'restaurants')['path'];
        }

        if ($coverImage instanceof UploadedFile) {
            if ($restaurant->cover_image) {
                $this->deleteImageAndThumb($restaurant->cover_image);
            }
            $restaurant->cover_image = $this->uploadCompressedImage($coverImage, 'restaurants/covers')['path'];
        }

        $restaurant->save();

        return $restaurant;
    }
}

