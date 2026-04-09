<?php

namespace App\Services\Backend;

use App\Models\DeliveryBoy;
use App\Traits\HandlesImageUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class DeliveryBoyService
{
    use HandlesImageUploads;

    public function upsert(array $data, ?DeliveryBoy $deliveryBoy = null): DeliveryBoy
    {
        /** @var UploadedFile|null $newImage */
        $newImage = Arr::pull($data, 'image');
        $removeImage = (bool) Arr::pull($data, 'remove_image', false);
        Arr::pull($data, 'redirect_page');

        $deliveryBoy ??= new DeliveryBoy();
        $deliveryBoy->fill($data);

        if ($removeImage && $deliveryBoy->image) {
            $this->deleteImageAndThumb($deliveryBoy->image);
            $deliveryBoy->image = null;
        }

        if ($newImage instanceof UploadedFile) {
            if ($deliveryBoy->image) {
                $this->deleteImageAndThumb($deliveryBoy->image);
            }

            $upload = $this->uploadCompressedImage($newImage, 'delivery-boys');
            $deliveryBoy->image = $upload['path'];
        }

        $deliveryBoy->save();

        return $deliveryBoy;
    }
}

