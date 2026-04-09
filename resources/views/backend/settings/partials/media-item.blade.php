@php
    $typeLabel = $type === 'slider' ? 'Slider' : 'Banner';
    $desktopExisting = $item['desktop_image_existing'] ?? $item['desktop_image'] ?? null;
    $mobileExisting = $item['mobile_image_existing'] ?? $item['mobile_image'] ?? null;
    $desktopPreview = $desktopExisting ? \App\Support\ImagePath::thumbUrl($desktopExisting, 'admin/assets/img/media/media-16.jpg') : null;
    $mobilePreview = $mobileExisting ? \App\Support\ImagePath::thumbUrl($mobileExisting, 'admin/assets/img/media/media-17.jpg') : null;
    $prefix = $type === '__TYPE__' ? '__TYPE__' : 'home_'.$type.'s';
@endphp

<div class="media-item-card" data-media-item>
    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
        <h5 class="mb-0">{{ $typeLabel }} Item</h5>
        <button type="button" class="btn btn-sm btn-outline-danger js-remove-media-item">
            <i class="fa-solid fa-trash me-1"></i>Remove
        </button>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Desktop Image</label>
            <input
                type="file"
                class="form-control"
                accept="image/*"
                data-media-preview-input="1"
                data-preview-target=".js-desktop-preview"
                data-empty-target=".js-desktop-empty"
                data-name-template="{{ $prefix }}[__INDEX__][desktop_image]"
            >
            <span class="media-helper">Recommended desktop size: 1920 x 760 px.</span>
            <input type="hidden" value="{{ $desktopExisting }}" data-name-template="{{ $prefix }}[__INDEX__][desktop_image_existing]">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" value="1" id="{{ $type }}-desktop-remove-{{ $index }}" data-name-template="{{ $prefix }}[__INDEX__][remove_desktop_image]">
                <label class="form-check-label" for="{{ $type }}-desktop-remove-{{ $index }}">Remove desktop image</label>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Desktop Preview</label>
            <img src="{{ $desktopPreview ?? '' }}" alt="Desktop preview" class="media-preview js-desktop-preview" style="{{ $desktopPreview ? '' : 'display:none;' }}">
            <div class="media-preview-empty js-desktop-empty" style="{{ $desktopPreview ? 'display:none;' : '' }}">Desktop image preview will appear here.</div>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Mobile Image</label>
            <input
                type="file"
                class="form-control"
                accept="image/*"
                data-media-preview-input="1"
                data-preview-target=".js-mobile-preview"
                data-empty-target=".js-mobile-empty"
                data-name-template="{{ $prefix }}[__INDEX__][mobile_image]"
            >
            <span class="media-helper">Recommended mobile size: 768 x 980 px.</span>
            <input type="hidden" value="{{ $mobileExisting }}" data-name-template="{{ $prefix }}[__INDEX__][mobile_image_existing]">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" value="1" id="{{ $type }}-mobile-remove-{{ $index }}" data-name-template="{{ $prefix }}[__INDEX__][remove_mobile_image]">
                <label class="form-check-label" for="{{ $type }}-mobile-remove-{{ $index }}">Remove mobile image</label>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Mobile Preview</label>
            <img src="{{ $mobilePreview ?? '' }}" alt="Mobile preview" class="media-preview js-mobile-preview" style="{{ $mobilePreview ? '' : 'display:none;' }}">
            <div class="media-preview-empty js-mobile-empty" style="{{ $mobilePreview ? 'display:none;' : '' }}">Mobile image preview will appear here.</div>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Heading</label>
            <input class="form-control" value="{{ $item['title'] ?? '' }}" placeholder="Main title" data-name-template="{{ $prefix }}[__INDEX__][title]">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Button Label</label>
            <input class="form-control" value="{{ $item['button_label'] ?? '' }}" placeholder="Order now" data-name-template="{{ $prefix }}[__INDEX__][button_label]">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Button URL</label>
            <input class="form-control" value="{{ $item['button_url'] ?? '' }}" placeholder="/menu" data-name-template="{{ $prefix }}[__INDEX__][button_url]">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Status</label>
            <select class="form-control" data-name-template="{{ $prefix }}[__INDEX__][is_active]">
                <option value="1" @selected((int) ($item['is_active'] ?? 1) === 1)>Active</option>
                <option value="0" @selected((int) ($item['is_active'] ?? 1) === 0)>Inactive</option>
            </select>
        </div>
        <div class="col-12 mb-0">
            <label class="form-label">Text Below Image</label>
            <textarea class="form-control" rows="3" placeholder="Short description or caption shown on frontend." data-name-template="{{ $prefix }}[__INDEX__][description]">{{ $item['description'] ?? '' }}</textarea>
        </div>
    </div>
</div>
