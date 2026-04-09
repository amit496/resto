<?php

namespace App\Services\Backend;

use App\Models\Customer;
use App\Traits\HandlesImageUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class CustomerService
{
    use HandlesImageUploads;

    public function upsert(array $data, ?Customer $customer = null): Customer
    {
        /** @var UploadedFile|null $newImage */
        $newImage = Arr::pull($data, 'image');
        $removeImage = (bool) Arr::pull($data, 'remove_image', false);
        Arr::pull($data, 'redirect_page');

        $customer ??= new Customer();
        $customer->fill($data);

        if ($removeImage && $customer->image) {
            $this->deleteImageAndThumb($customer->image);
            $customer->image = null;
        }

        if ($newImage instanceof UploadedFile) {
            if ($customer->image) {
                $this->deleteImageAndThumb($customer->image);
            }

            $upload = $this->uploadCompressedImage($newImage, 'customers');
            $customer->image = $upload['path'];
        }

        $customer->save();

        return $customer;
    }
}

