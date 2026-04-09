@extends('frontend.layouts.app')

@section('title', ($setting->app_name ?? config('app.name', 'FoodiHub')).' | Branches')

@section('content')
    <section class="page-hero">
        <div class="container order-banner">
            <div class="order-banner-card primary">
                <div class="eyebrow">Branch Directory</div>
                <h1 class="section-title">Browse every active branch under one restaurant brand.</h1>
                <p class="section-copy">Commercial-style listing layout with stronger imagery, operational chips and cleaner branch discovery.</p>
            </div>
            <div class="order-banner-card dark">
                <div class="eyebrow" style="background: rgba(255,255,255,0.12); color:#fff;">Branch Signals</div>
                <div class="trust-row">
                    <div class="trust-chip">Timing visible</div>
                    <div class="trust-chip">Manager info</div>
                    <div class="trust-chip">Delivery radius</div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-sm">
        <div class="container grid-3">
            @foreach($branches as $branch)
                <a href="{{ route('frontend.branches.show', $branch) }}" class="branch-card">
                    <img src="{{ \App\Support\ImagePath::thumbUrl($branch->image, \App\Support\FoodImageResolver::category('drinks')) }}" alt="{{ $branch->name }}" class="media-thumb">
                    <h3>{{ $branch->name }}</h3>
                    <p class="muted">{{ $branch->address ?: 'Address available on branch detail page.' }}</p>
                    <div class="branch-meta">
                        @if($branch->opening_time && $branch->closing_time)
                            <span class="pill">{{ $branch->opening_time }} - {{ $branch->closing_time }}</span>
                        @endif
                        @if($branch->delivery_radius_km)
                            <span class="pill">{{ $branch->delivery_radius_km }} KM Radius</span>
                        @endif
                        @if($branch->manager_name)
                            <span class="pill">{{ $branch->manager_name }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
        <div class="section-sm">{{ $branches->links() }}</div>
    </section>
@endsection

