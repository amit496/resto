<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpsertSettingRequest;
use App\Models\AppSetting;
use App\Models\Restaurant;
use App\Services\Backend\SettingService;
use App\Support\CountryCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $service)
    {
    }

    public function edit(): View
    {
        return view('backend.settings.form', [
            'setting' => AppSetting::query()->first() ?? new AppSetting(),
            'restaurants' => Restaurant::query()->orderBy('name')->get(),
            'countries' => CountryCatalog::options(),
        ]);
    }

    public function update(UpsertSettingRequest $request): RedirectResponse
    {
        $this->service->upsert($request->validated());

        return redirect()->route('admin.settings.edit')->with('success', 'Settings updated.');
    }
}

