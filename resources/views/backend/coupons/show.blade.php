@extends('admin.layout.index')
@section('title', 'Coupon Details')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Coupon Details</h3>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-list me-1"></i>Back To List</a>
    </div>

    <div class="card"><div class="card-body">
        <table class="table table-bordered mb-0">
            <tr><th style="width:220px;">Code</th><td>{{ $coupon->code }}</td></tr>
            <tr><th>Type</th><td>{{ $coupon->type->value }}</td></tr>
            <tr><th>Value</th><td>{{ $coupon->value }}</td></tr>
            <tr><th>Min Order Amount</th><td>{{ $coupon->min_order_amount }}</td></tr>
            <tr><th>Start Date</th><td>{{ $coupon->start_date?->format('d M Y') ?: '-' }}</td></tr>
            <tr><th>End Date</th><td>{{ $coupon->end_date?->format('d M Y') ?: '-' }}</td></tr>
            <tr><th>Status</th><td>{{ $coupon->status->value }}</td></tr>
            <tr><th>Restaurant</th><td>{{ $coupon->restaurant?->name }}</td></tr>
        </table>
    </div></div>
@endsection


