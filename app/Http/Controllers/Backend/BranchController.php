<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Restaurant;
use App\Traits\HandlesImageUploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BranchController extends Controller
{
    use HandlesImageUploads;

    public function index(Request $request): View
    {
        $singleRestaurantMode = (bool) config('app.single_restaurant_mode', true);
        $restaurantId = $singleRestaurantMode
            ? Restaurant::query()->value('id')
            : $request->integer('restaurant_id');

        return view('backend.branches.index', [
            'branches' => Branch::query()
                ->with('restaurant')
                ->when($restaurantId, fn ($query) => $query->where('restaurant_id', $restaurantId))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'restaurants' => Restaurant::query()->orderBy('name')->get(['id', 'name']),
            'singleRestaurantMode' => $singleRestaurantMode,
            'selectedRestaurantId' => $restaurantId,
        ]);
    }

    public function show(Branch $branch): View
    {
        return view('backend.branches.show', [
            'branch' => $branch->load('restaurant'),
        ]);
    }

    public function edit(Branch $branch): View
    {
        return view('backend.branches.form', [
            'branch' => $branch->load('restaurant'),
            'restaurants' => Restaurant::query()->orderBy('name')->get(['id', 'name']),
            'singleRestaurantMode' => (bool) config('app.single_restaurant_mode', true),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $singleRestaurantMode = (bool) config('app.single_restaurant_mode', true);
        $restaurantId = $singleRestaurantMode
            ? Restaurant::query()->value('id')
            : $request->integer('restaurant_id');

        $data = $request->validate([
            'restaurant_id' => [
                Rule::requiredIf(! $singleRestaurantMode),
                'nullable',
                'exists:restaurants,id',
            ],
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:30'],
            'manager_name' => ['nullable', 'string', 'max:120'],
            'manager_phone' => ['nullable', 'string', 'max:30'],
            'manager_email' => ['nullable', 'email', 'max:120'],
            'manager_photo' => ['nullable', 'image', 'max:4096'],
            'address' => ['nullable', 'string'],
            'opening_time' => ['nullable', 'date_format:H:i'],
            'closing_time' => ['nullable', 'date_format:H:i'],
            'weekly_off' => ['nullable', 'string', 'max:50'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'delivery_radius_km' => ['nullable', 'numeric', 'min:0'],
            'gst_no' => ['nullable', 'string', 'max:30'],
            'fssai_no' => ['nullable', 'string', 'max:30'],
            'image' => ['nullable', 'image', 'max:4096'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if (! $restaurantId) {
            return back()->with('error', 'Please create a restaurant profile first.');
        }

        /** @var UploadedFile|null $image */
        $image = Arr::pull($data, 'image');
        /** @var UploadedFile|null $managerPhoto */
        $managerPhoto = Arr::pull($data, 'manager_photo');

        $branch = Branch::query()->create([
            ...$data,
            'restaurant_id' => $restaurantId,
        ]);

        if ($image instanceof UploadedFile) {
            $branch->update([
                'image' => $this->uploadCompressedImage($image, 'branches')['path'],
            ]);
        }

        if ($managerPhoto instanceof UploadedFile) {
            $branch->update([
                'manager_photo' => $this->uploadCompressedImage($managerPhoto, 'branches/managers')['path'],
            ]);
        }

        return back()->with('success', 'Branch created.');
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:30'],
            'manager_name' => ['nullable', 'string', 'max:120'],
            'manager_phone' => ['nullable', 'string', 'max:30'],
            'manager_email' => ['nullable', 'email', 'max:120'],
            'manager_photo' => ['nullable', 'image', 'max:4096'],
            'remove_manager_photo' => ['nullable', 'boolean'],
            'address' => ['nullable', 'string'],
            'opening_time' => ['nullable', 'date_format:H:i'],
            'closing_time' => ['nullable', 'date_format:H:i'],
            'weekly_off' => ['nullable', 'string', 'max:50'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'delivery_radius_km' => ['nullable', 'numeric', 'min:0'],
            'gst_no' => ['nullable', 'string', 'max:30'],
            'fssai_no' => ['nullable', 'string', 'max:30'],
            'image' => ['nullable', 'image', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        /** @var UploadedFile|null $image */
        $image = Arr::pull($data, 'image');
        /** @var UploadedFile|null $managerPhoto */
        $managerPhoto = Arr::pull($data, 'manager_photo');
        $removeImage = (bool) Arr::pull($data, 'remove_image', false);
        $removeManagerPhoto = (bool) Arr::pull($data, 'remove_manager_photo', false);

        if ($removeImage && $branch->image) {
            $this->deleteImageAndThumb($branch->image);
            $branch->image = null;
        }

        if ($image instanceof UploadedFile) {
            if ($branch->image) {
                $this->deleteImageAndThumb($branch->image);
            }
            $branch->image = $this->uploadCompressedImage($image, 'branches')['path'];
        }

        if ($removeManagerPhoto && $branch->manager_photo) {
            $this->deleteImageAndThumb($branch->manager_photo);
            $branch->manager_photo = null;
        }

        if ($managerPhoto instanceof UploadedFile) {
            if ($branch->manager_photo) {
                $this->deleteImageAndThumb($branch->manager_photo);
            }
            $branch->manager_photo = $this->uploadCompressedImage($managerPhoto, 'branches/managers')['path'];
        }

        $branch->update($data);
        $branch->save();

        return back()->with('success', 'Branch updated.');
    }

    public function toggleStatus(Branch $branch): RedirectResponse
    {
        $current = $branch->status?->value ?? (string) $branch->status;
        $branch->update([
            'status' => $current === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Branch status updated.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $branch->update(['status' => 'inactive']);

        return back()->with('success', 'Branch archived.');
    }
}

