@php
    $prefix = $index === '__INDEX__' ? 'home_how_it_works[__INDEX__]' : "home_how_it_works[{$index}]";
@endphp

<div class="media-item-card" data-how-step data-how-index="{{ $index }}">
    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
        <h5 class="mb-0">How it works — step</h5>
        <button type="button" class="btn btn-sm btn-outline-danger js-remove-how-step">
            <i class="fa-solid fa-trash me-1"></i>Remove
        </button>
    </div>
    <div class="row">
        <div class="col-md-3 mb-3">
            <label class="form-label">Icon (emoji)</label>
            <input class="form-control" name="{{ $prefix }}[icon]" value="{{ $item['icon'] ?? '' }}" placeholder="📍" maxlength="40">
        </div>
        <div class="col-md-5 mb-3">
            <label class="form-label">Title</label>
            <input class="form-control" name="{{ $prefix }}[title]" value="{{ $item['title'] ?? '' }}" placeholder="Choose restaurant" maxlength="160">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Status</label>
            <select class="form-control" name="{{ $prefix }}[is_active]">
                <option value="1" @selected((int) ($item['is_active'] ?? 1) === 1)>Active</option>
                <option value="0" @selected((int) ($item['is_active'] ?? 1) === 0)>Inactive</option>
            </select>
        </div>
        <div class="col-12 mb-0">
            <label class="form-label">Description</label>
            <textarea class="form-control" rows="2" name="{{ $prefix }}[description]" placeholder="Short line shown on the storefront homepage." maxlength="500">{{ $item['description'] ?? '' }}</textarea>
        </div>
    </div>
</div>
