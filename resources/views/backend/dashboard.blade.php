@extends('admin.layout.index')

@section('title', 'Backend Dashboard')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Foodihub</h3>
            <p class="text-muted mb-0">Backend modules overview</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <h6>Restaurants</h6>
                    <h3>{{ $restaurantsCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <h6>Categories</h6>
                    <h3>{{ $categoriesCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <h6>Products</h6>
                    <h3>{{ $productsCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <h6>Orders</h6>
                    <h3>{{ $ordersCount }}</h3>
                </div>
            </div>
        </div>
    </div>
@endsection

