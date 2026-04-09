@extends('admin.layout.index')
@section('title', $subcategory->exists ? 'Edit Subcategory' : 'Add Subcategory')
@section('content')
    <div class="mb-4"><h3 class="mb-0">{{ $subcategory->exists ? 'Edit Subcategory' : 'Add Subcategory' }}</h3></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ $subcategory->exists ? route('admin.subcategories.update', $subcategory) : route('admin.subcategories.store') }}">
            @csrf
            @if($subcategory->exists) @method('PUT') @endif
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-control" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $subcategory->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ old('name', $subcategory->name) }}" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">Slug</label><input name="slug" class="form-control" value="{{ old('slug', $subcategory->slug) }}"></div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" @selected(old('status', $subcategory->status?->value ?? 'active') === 'active')>active</option>
                        <option value="inactive" @selected(old('status', $subcategory->status?->value) === 'inactive')>inactive</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary">Save</button>
        </form>
    </div></div>
@endsection

