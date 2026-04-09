@extends('admin.layout.index')
@section('title', 'Settings')

@php
    $meta = $setting->meta ?? [];
    $sliderItems = old('home_sliders', $meta['home_sliders'] ?? [[]]);
    $bannerItems = old('home_banners', $meta['home_banners'] ?? [[]]);
    $howSteps = old('home_how_it_works', $meta['home_how_it_works'] ?? []);
    $howSteps = is_array($howSteps) && $howSteps !== [] ? $howSteps : [[]];
    $promoPopup = old('home_promo_popup', $meta['home_promo_popup'] ?? []);
    $footerAppLinks = old('footer_app_links', $meta['footer_app_links'] ?? []);
@endphp

@section('content')
    <style>
        .media-builder {
            display: grid;
            gap: 16px;
        }
        .media-item-card {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 18px;
            background: #fff;
        }
        .media-item-card + .media-item-card {
            margin-top: 16px;
        }
        .media-preview {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
        }
        .media-preview-empty {
            min-height: 140px;
            display: grid;
            place-items: center;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            color: #64748b;
            background: #f8fafc;
            text-align: center;
            padding: 12px;
        }
        .media-helper {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: #64748b;
        }
    </style>

    <div class="mb-4"><h3 class="mb-0">Settings</h3></div>
    <div class="alert alert-info">
        Homepage slider and banner management is available in the <strong>Homepage Media</strong> tab below.
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')

                <ul class="nav nav-pills gap-2 mb-4" id="settings-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-general" type="button" role="tab">
                            <i class="fa-solid fa-sliders me-1"></i>General
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-financial" type="button" role="tab">
                            <i class="fa-solid fa-money-bill-wave me-1"></i>Financial
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-payments" type="button" role="tab">
                            <i class="fa-solid fa-credit-card me-1"></i>Payments
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-order-delivery" type="button" role="tab">
                            <i class="fa-solid fa-bag-shopping me-1"></i>Order & Delivery
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-operations" type="button" role="tab">
                            <i class="fa-solid fa-gears me-1"></i>Operations
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-branding" type="button" role="tab">
                            <i class="fa-solid fa-paint-roller me-1"></i>Support & Branding
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-homepage-media" type="button" role="tab">
                            <i class="fa-solid fa-images me-1"></i>Homepage Media
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-homepage-blocks" type="button" role="tab">
                            <i class="fa-solid fa-layer-group me-1"></i>Homepage blocks
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-general" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Restaurant</label>
                                <select class="form-control" name="restaurant_id">
                                    <option value="">Global</option>
                                    @foreach($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}" @selected(old('restaurant_id', $setting->restaurant_id) == $restaurant->id)>{{ $restaurant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">App Name</label>
                                <input class="form-control" name="app_name" value="{{ old('app_name', $setting->app_name ?? 'Foodihub') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Timezone</label>
                                <input class="form-control" name="timezone" value="{{ old('timezone', $setting->timezone ?? 'Asia/Calcutta') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Order Prefix</label>
                                <input class="form-control" name="order_prefix" value="{{ old('order_prefix', $setting->order_prefix ?? 'ORD') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-financial" role="tabpanel">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Currency</label>
                                <input class="form-control" name="currency" value="{{ old('currency', $setting->currency ?? 'INR') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Currency Symbol</label>
                                <input class="form-control" name="currency_symbol" value="{{ old('currency_symbol', $setting->currency_symbol ?? 'Rs') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Decimal Places</label>
                                <input class="form-control" type="number" name="decimal_places" value="{{ old('decimal_places', $setting->decimal_places ?? 2) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Min Order Amount</label>
                                <input class="form-control" type="number" step="0.01" name="min_order_amount" value="{{ old('min_order_amount', $setting->min_order_amount ?? 0) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tax (%)</label>
                                <input class="form-control" type="number" step="0.01" name="tax_percent" value="{{ old('tax_percent', $setting->tax_percent ?? 0) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Service Charge</label>
                                <input class="form-control" type="number" step="0.01" name="service_charge" value="{{ old('service_charge', $setting->service_charge ?? 0) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Delivery Fee</label>
                                <input class="form-control" type="number" step="0.01" name="delivery_fee" value="{{ old('delivery_fee', $setting->delivery_fee ?? 0) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Packing Fee</label>
                                <input class="form-control" type="number" step="0.01" name="packing_fee" value="{{ old('packing_fee', $setting->packing_fee ?? 0) }}">
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-payments" role="tabpanel">
                        <div class="alert alert-light border">
                            Configure the gateways that should appear on the customer checkout page for each country.
                        </div>
                        @include('backend.settings.partials.payment-gateways', ['countries' => $countries, 'meta' => $meta])
                    </div>

                    <div class="tab-pane fade" id="tab-order-delivery" role="tabpanel">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Prep Time (min)</label>
                                <input class="form-control" type="number" name="est_prep_time_min" value="{{ old('est_prep_time_min', $setting->est_prep_time_min ?? 20) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Delivery Time (min)</label>
                                <input class="form-control" type="number" name="est_delivery_time_min" value="{{ old('est_delivery_time_min', $setting->est_delivery_time_min ?? 40) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Max Delivery KM</label>
                                <input class="form-control" type="number" name="max_delivery_km" value="{{ old('max_delivery_km', $setting->max_delivery_km ?? 10) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Allow Scheduled Orders</label>
                                <select class="form-control" name="allow_scheduled_orders">
                                    <option value="1" @selected(old('allow_scheduled_orders', $setting->allow_scheduled_orders ?? true))>Yes</option>
                                    <option value="0" @selected(!old('allow_scheduled_orders', $setting->allow_scheduled_orders ?? true))>No</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Allow Guest Checkout</label>
                                <select class="form-control" name="allow_guest_checkout">
                                    <option value="1" @selected(old('allow_guest_checkout', $setting->allow_guest_checkout ?? true))>Yes</option>
                                    <option value="0" @selected(!old('allow_guest_checkout', $setting->allow_guest_checkout ?? true))>No</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Auto Accept Orders</label>
                                <select class="form-control" name="auto_accept_orders">
                                    <option value="1" @selected(old('auto_accept_orders', $setting->auto_accept_orders ?? false))>Yes</option>
                                    <option value="0" @selected(!old('auto_accept_orders', $setting->auto_accept_orders ?? false))>No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-operations" role="tabpanel">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Enable Coupons</label>
                                <select class="form-control" name="enable_coupons">
                                    <option value="1" @selected(old('enable_coupons', $setting->enable_coupons ?? true))>Yes</option>
                                    <option value="0" @selected(!old('enable_coupons', $setting->enable_coupons ?? true))>No</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Enable Tips</label>
                                <select class="form-control" name="enable_tips">
                                    <option value="1" @selected(old('enable_tips', $setting->enable_tips ?? true))>Yes</option>
                                    <option value="0" @selected(!old('enable_tips', $setting->enable_tips ?? true))>No</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Enable KOT</label>
                                <select class="form-control" name="enable_kot">
                                    <option value="1" @selected(old('enable_kot', $setting->enable_kot ?? true))>Yes</option>
                                    <option value="0" @selected(!old('enable_kot', $setting->enable_kot ?? true))>No</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Enable Stock Deduction</label>
                                <select class="form-control" name="enable_stock_deduction">
                                    <option value="1" @selected(old('enable_stock_deduction', $setting->enable_stock_deduction ?? true))>Yes</option>
                                    <option value="0" @selected(!old('enable_stock_deduction', $setting->enable_stock_deduction ?? true))>No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-branding" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Support Phone</label>
                                <input class="form-control" name="support_phone" value="{{ old('support_phone', $setting->support_phone) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Support Email</label>
                                <input class="form-control" name="support_email" value="{{ old('support_email', $setting->support_email) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Theme Color</label>
                                <input class="form-control" name="theme_color" value="{{ old('theme_color', $setting->theme_color ?? '#F59E0B') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Logo Path</label>
                                <input class="form-control" name="app_logo" value="{{ old('app_logo', $setting->app_logo) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Favicon Path</label>
                                <input class="form-control" name="app_favicon" value="{{ old('app_favicon', $setting->app_favicon) }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Invoice Footer</label>
                                <textarea class="form-control" name="invoice_footer">{{ old('invoice_footer', $setting->invoice_footer) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-homepage-media" role="tabpanel">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                            <div>
                                <h4 class="mb-1">Homepage Slider</h4>
                                <p class="text-muted mb-0">Admin can manage desktop/mobile slider images with text and buttons. Frontend will switch image automatically based on screen size.</p>
                            </div>
                            <button type="button" class="btn btn-outline-primary js-add-media-item" data-target="#slider-builder" data-type="slider">
                                <i class="fa-solid fa-plus me-1"></i>Add Slider
                            </button>
                        </div>

                        <div id="slider-builder" class="media-builder" data-type="slider">
                            @foreach($sliderItems as $index => $item)
                                @include('backend.settings.partials.media-item', ['type' => 'slider', 'index' => $index, 'item' => $item])
                            @endforeach
                        </div>

                        <hr class="my-4">

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                            <div>
                                <h4 class="mb-1">Homepage Banner</h4>
                                <p class="text-muted mb-0">Add promotional banners with separate mobile and desktop images. The text below each image is shown on the user frontend.</p>
                            </div>
                            <button type="button" class="btn btn-outline-primary js-add-media-item" data-target="#banner-builder" data-type="banner">
                                <i class="fa-solid fa-plus me-1"></i>Add Banner
                            </button>
                        </div>

                        <div id="banner-builder" class="media-builder" data-type="banner">
                            @foreach($bannerItems as $index => $item)
                                @include('backend.settings.partials.media-item', ['type' => 'banner', 'index' => $index, 'item' => $item])
                            @endforeach
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-homepage-blocks" role="tabpanel">
                        <div class="alert alert-light border mb-4">
                            <strong>How it works</strong>, <strong>promo popup</strong>, and <strong>app store links</strong> render on the customer storefront (homepage + footer). Leave popup inactive until copy is ready.
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                            <div>
                                <h4 class="mb-1">How it works</h4>
                                <p class="text-muted mb-0">Three steps work best (browse → order → enjoy). Shown as a responsive grid on the homepage.</p>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="js-add-how-step">
                                <i class="fa-solid fa-plus me-1"></i>Add step
                            </button>
                        </div>
                        <div id="how-steps-builder" class="media-builder">
                            @foreach($howSteps as $index => $item)
                                @include('backend.settings.partials.how-it-works-step', ['index' => $index, 'item' => is_array($item) ? $item : []])
                            @endforeach
                        </div>

                        <hr class="my-4">

                        <h4 class="mb-3">Promotional popup</h4>
                        <div class="media-item-card mb-4">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" name="home_promo_popup[is_active]">
                                        <option value="0" @selected((int) ($promoPopup['is_active'] ?? 0) === 0)>Inactive</option>
                                        <option value="1" @selected((int) ($promoPopup['is_active'] ?? 0) === 1)>Active</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Delay (seconds)</label>
                                    <input class="form-control" type="number" name="home_promo_popup[delay_seconds]" min="0" max="600" value="{{ old('home_promo_popup.delay_seconds', $promoPopup['delay_seconds'] ?? 2) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Button label</label>
                                    <input class="form-control" name="home_promo_popup[button_label]" value="{{ old('home_promo_popup.button_label', $promoPopup['button_label'] ?? '') }}" placeholder="Order now" maxlength="80">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title</label>
                                    <input class="form-control" name="home_promo_popup[title]" value="{{ old('home_promo_popup.title', $promoPopup['title'] ?? '') }}" maxlength="160">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Button URL</label>
                                    <input class="form-control" name="home_promo_popup[button_url]" value="{{ old('home_promo_popup.button_url', $promoPopup['button_url'] ?? '') }}" maxlength="500">
                                </div>
                                <div class="col-12 mb-0">
                                    <label class="form-label">Message</label>
                                    <textarea class="form-control" rows="3" name="home_promo_popup[body]" maxlength="800">{{ old('home_promo_popup.body', $promoPopup['body'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <h4 class="mb-3">Get the app (footer)</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">App Store (iOS) URL</label>
                                <input class="form-control" name="footer_app_links[ios_url]" value="{{ old('footer_app_links.ios_url', $footerAppLinks['ios_url'] ?? '') }}" placeholder="https://apps.apple.com/..." maxlength="500">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Google Play URL</label>
                                <input class="form-control" name="footer_app_links[android_url]" value="{{ old('footer_app_links.android_url', $footerAppLinks['android_url'] ?? '') }}" placeholder="https://play.google.com/..." maxlength="500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Save Settings</button>
                </div>
            </form>
        </div>
    </div>

    <template id="media-item-template">
        @include('backend.settings.partials.media-item', ['type' => '__TYPE__', 'index' => '__INDEX__', 'item' => []])
    </template>

    <template id="how-step-template">
        @include('backend.settings.partials.how-it-works-step', ['index' => '__INDEX__', 'item' => []])
    </template>

    <script>
        (function () {
            const template = document.getElementById('media-item-template');
            const activateHashTab = () => {
                if (!window.location.hash) {
                    return;
                }

                const trigger = document.querySelector('[data-bs-target="' + window.location.hash + '"]');
                if (!trigger || !window.bootstrap || !window.bootstrap.Tab) {
                    return;
                }

                window.bootstrap.Tab.getOrCreateInstance(trigger).show();
            };

            const bindPreview = (scope) => {
                scope.querySelectorAll('[data-media-preview-input]').forEach((input) => {
                    if (input.dataset.boundPreview === '1') {
                        return;
                    }

                    input.dataset.boundPreview = '1';
                    input.addEventListener('change', (event) => {
                        const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
                        const previewTarget = event.target.dataset.previewTarget;
                        const emptyTarget = event.target.dataset.emptyTarget;
                        const preview = previewTarget ? scope.querySelector(previewTarget) : null;
                        const empty = emptyTarget ? scope.querySelector(emptyTarget) : null;

                        if (!file || !preview) {
                            return;
                        }

                        preview.src = URL.createObjectURL(file);
                        preview.style.display = 'block';
                        if (empty) {
                            empty.style.display = 'none';
                        }
                    });
                });
            };

            const refreshIndexes = (builder) => {
                const type = builder.dataset.type;
                builder.querySelectorAll('[data-media-item]').forEach((card, index) => {
                    card.querySelectorAll('[data-name-template]').forEach((field) => {
                        field.name = field.dataset.nameTemplate.replace(/__TYPE__/g, type).replace(/__INDEX__/g, index);
                    });
                });
            };

            document.querySelectorAll('.media-builder').forEach((builder) => {
                bindPreview(builder);
                refreshIndexes(builder);
            });

            document.querySelectorAll('.js-add-media-item').forEach((button) => {
                button.addEventListener('click', () => {
                    const builder = document.querySelector(button.dataset.target);
                    if (!builder || !template) {
                        return;
                    }

                    const type = button.dataset.type;
                    const nextIndex = builder.querySelectorAll('[data-media-item]').length;
                    const html = template.innerHTML
                        .replace(/__TYPE__/g, type)
                        .replace(/__INDEX__/g, nextIndex)
                        .trim();

                    builder.insertAdjacentHTML('beforeend', html);
                    const newCard = builder.lastElementChild;
                    if (newCard) {
                        bindPreview(newCard);
                    }
                    refreshIndexes(builder);
                });
            });

            document.addEventListener('click', (event) => {
                const removeButton = event.target.closest('.js-remove-media-item');
                if (!removeButton) {
                    return;
                }

                const card = removeButton.closest('[data-media-item]');
                const builder = card ? card.parentElement : null;
                if (!card || !builder) {
                    return;
                }

                card.remove();

                if (!builder.querySelector('[data-media-item]')) {
                    const addButton = document.querySelector('.js-add-media-item[data-target="#' + builder.id + '"]');
                    if (addButton) {
                        addButton.click();
                    }
                } else {
                    refreshIndexes(builder);
                }
            });

            const howTemplate = document.getElementById('how-step-template');
            const howBuilder = document.getElementById('how-steps-builder');
            const refreshHowIndexes = () => {
                if (!howBuilder) {
                    return;
                }
                howBuilder.querySelectorAll('[data-how-step]').forEach((card, index) => {
                    card.querySelectorAll('[name]').forEach((field) => {
                        field.name = field.name.replace(/home_how_it_works\[\d+]/, 'home_how_it_works[' + index + ']');
                    });
                });
            };

            document.getElementById('js-add-how-step')?.addEventListener('click', () => {
                if (!howBuilder || !howTemplate) {
                    return;
                }
                const nextIndex = howBuilder.querySelectorAll('[data-how-step]').length;
                const html = howTemplate.innerHTML.replace(/__INDEX__/g, String(nextIndex)).trim();
                howBuilder.insertAdjacentHTML('beforeend', html);
                refreshHowIndexes();
            });

            document.addEventListener('click', (event) => {
                const btn = event.target.closest('.js-remove-how-step');
                if (!btn || !howBuilder) {
                    return;
                }
                const card = btn.closest('[data-how-step]');
                if (!card) {
                    return;
                }
                card.remove();
                if (!howBuilder.querySelector('[data-how-step]')) {
                    document.getElementById('js-add-how-step')?.click();
                } else {
                    refreshHowIndexes();
                }
            });

            refreshHowIndexes();

            activateHashTab();
            window.addEventListener('hashchange', activateHashTab);
        })();
    </script>
@endsection
