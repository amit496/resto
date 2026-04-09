@extends('admin.layout.index')
@section('title', 'Category Details')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Category Details</h3>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-list me-1"></i>Back To List</a>
    </div>

    <div class="card"><div class="card-body">
        <table class="table table-bordered mb-0">
            <tr><th style="width:220px;">Image</th><td><img src="{{ \App\Support\ImagePath::thumbUrl($category->image, \App\Support\FoodImageResolver::category($category->name)) }}" style="width:72px;height:72px;object-fit:cover;border-radius:10px;"></td></tr>
            <tr><th>Name</th><td>{{ $category->name }}</td></tr>
            <tr><th>Slug</th><td>{{ $category->slug }}</td></tr>
            <tr><th>Restaurant</th><td>{{ $category->restaurant?->name }}</td></tr>
            <tr><th>Status</th><td>{{ $category->status->value }}</td></tr>
            <tr><th>Subcategories</th><td>{{ $category->subcategories->count() }}</td></tr>
        </table>
    </div></div>
@endsection


