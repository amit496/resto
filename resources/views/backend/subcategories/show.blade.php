@extends('admin.layout.index')
@section('title', 'Subcategory Details')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Subcategory Details</h3>
        <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-list me-1"></i>Back To List</a>
    </div>

    <div class="card"><div class="card-body">
        <table class="table table-bordered mb-0">
            <tr><th style="width:220px;">Name</th><td>{{ $subcategory->name }}</td></tr>
            <tr><th>Slug</th><td>{{ $subcategory->slug }}</td></tr>
            <tr><th>Category</th><td>{{ $subcategory->category?->name }}</td></tr>
            <tr><th>Restaurant</th><td>{{ $subcategory->category?->restaurant?->name }}</td></tr>
            <tr><th>Status</th><td>{{ $subcategory->status->value }}</td></tr>
        </table>
    </div></div>
@endsection


