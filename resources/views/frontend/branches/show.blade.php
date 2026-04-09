@extends('frontend.layouts.app')

@section('title', $branch->name.' | '.($setting->app_name ?? config('app.name', 'FoodiHub')))

@section('styles')
    <style>
        .branch-detail-layout {
            display: grid;
            gap: 22px;
        }
        .branch-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(340px, .65fr);
            gap: 20px;
            align-items: stretch;
        }
        .branch-visual,
        .branch-panel,
        .branch-section {
            border-radius: 34px;
            background: var(--surface);
            border: 1px solid rgba(255,255,255,0.74);
            box-shadow: var(--shadow);
            backdrop-filter: blur(16px);
        }
        .branch-visual {
            overflow: hidden;
            padding: 18px;
        }
        .branch-photo {
            width: 100%;
            height: 100%;
            min-height: 560px;
            object-fit: cover;
            border-radius: 26px;
            background: linear-gradient(135deg, rgba(203,32,45,0.16), rgba(17,17,17,0.64));
        }
        .branch-panel {
            display: grid;
            gap: 18px;
            padding: 24px;
            align-content: start;
        }
        .branch-name {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            font-size: clamp(42px, 4vw, 64px);
            line-height: .92;
            letter-spacing: -.06em;
        }
        .branch-summary {
            display: grid;
            gap: 10px;
        }
        .branch-address {
            margin: 0;
            font-size: 16px;
            line-height: 1.65;
            color: var(--muted);
        }
        .branch-meta-grid {
            display: grid;
            gap: 10px;
        }
        .branch-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 20px;
            background: rgba(255,255,255,0.7);
            border: 1px solid var(--line);
        }
        .branch-meta-label {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .branch-meta-value {
            text-align: right;
            font-weight: 800;
            overflow-wrap: anywhere;
            min-width: 0;
        }
        .branch-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .branch-chip-row .pill {
            min-height: 34px;
            padding: 0 12px;
            background: rgba(255,255,255,0.74);
        }
        .branch-section {
            padding: 24px;
        }
        .branch-section-title {
            margin: 0 0 18px;
            font-family: 'Outfit', sans-serif;
            font-size: clamp(28px, 3vw, 42px);
            letter-spacing: -.05em;
        }
        .branch-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }
        .branch-info-card {
            display: grid;
            gap: 10px;
            padding: 18px;
            border-radius: 24px;
            background: rgba(255,255,255,0.74);
            border: 1px solid var(--line);
        }
        .branch-info-card h3 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
        }
        .branch-info-card p {
            margin: 0;
            font-size: 16px;
            line-height: 1.6;
            font-weight: 700;
        }
        .branch-manager {
            display: grid;
            grid-template-columns: 112px 1fr;
            gap: 16px;
            align-items: center;
            padding: 18px;
            border-radius: 28px;
            background: linear-gradient(135deg, rgba(255,255,255,0.86), rgba(255,242,233,0.9));
            border: 1px solid rgba(255,255,255,0.76);
        }
        .branch-manager-photo {
            width: 112px;
            height: 112px;
            border-radius: 28px;
            object-fit: cover;
            background: linear-gradient(135deg, rgba(203,32,45,0.1), rgba(17,17,17,0.12));
        }
        .branch-manager-placeholder {
            display: grid;
            place-items: center;
            width: 112px;
            height: 112px;
            border-radius: 28px;
            background: linear-gradient(135deg, rgba(203,32,45,0.14), rgba(17,17,17,0.08));
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
            text-align: center;
        }
        .branch-manager-name {
            margin: 0 0 6px;
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            letter-spacing: -.04em;
        }
        .branch-manager-copy {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }
        .branch-link-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        @media (max-width: 960px) {
            .branch-hero,
            .branch-grid {
                grid-template-columns: 1fr;
            }

            .branch-photo {
                min-height: 380px;
            }

            .branch-manager {
                grid-template-columns: 1fr;
            }

            .branch-manager-photo,
            .branch-manager-placeholder {
                width: 100%;
                height: 220px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="page-hero">
        <div class="container branch-detail-layout">
            <div class="branch-hero">
                <div class="branch-visual">
                    @if($branch->image)
                        <img
                            src="{{ \App\Support\ImagePath::url($branch->image, \App\Support\FoodImageResolver::category('drinks')) }}"
                            alt="{{ $branch->name }}"
                            class="branch-photo"
                        >
                    @else
                        <div class="branch-photo"></div>
                    @endif
                </div>

                <div class="branch-panel">
                    <div class="eyebrow">Branch Detail</div>
                    <h1 class="branch-name">{{ $branch->name }}</h1>
                    <div class="branch-summary">
                        <p class="branch-address">{{ $branch->address ?: 'Branch address is managed from the admin panel.' }}</p>
                        <div class="branch-chip-row">
                            @if($branch->opening_time && $branch->closing_time)
                                <span class="pill">{{ $branch->opening_time }} - {{ $branch->closing_time }}</span>
                            @endif
                            @if($branch->delivery_radius_km)
                                <span class="pill">{{ $branch->delivery_radius_km }} KM Radius</span>
                            @endif
                            @if($branch->weekly_off)
                                <span class="pill">Weekly Off: {{ $branch->weekly_off }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="branch-meta-grid">
                        <div class="branch-meta-row">
                            <span class="branch-meta-label">Branch Phone</span>
                            <span class="branch-meta-value">{{ $branch->phone ?: '-' }}</span>
                        </div>
                        <div class="branch-meta-row">
                            <span class="branch-meta-label">Manager Email</span>
                            <span class="branch-meta-value">{{ $branch->manager_email ?: '-' }}</span>
                        </div>
                        <div class="branch-meta-row">
                            <span class="branch-meta-label">GST No</span>
                            <span class="branch-meta-value">{{ $branch->gst_no ?: '-' }}</span>
                        </div>
                        <div class="branch-meta-row">
                            <span class="branch-meta-label">FSSAI No</span>
                            <span class="branch-meta-value">{{ $branch->fssai_no ?: '-' }}</span>
                        </div>
                    </div>

                    <div class="branch-link-row">
                        <a href="{{ route('frontend.branches.index') }}" class="btn">View All Branches</a>
                        <a href="{{ route('frontend.contact') }}" class="btn-outline">Contact Team</a>
                    </div>
                </div>
            </div>

            <div class="branch-grid">
                <div class="branch-section">
                    <h2 class="branch-section-title">Operational Info</h2>
                    <div class="branch-grid">
                        <div class="branch-info-card">
                            <h3>Manager</h3>
                            <p>{{ $branch->manager_name ?: '-' }}</p>
                        </div>
                        <div class="branch-info-card">
                            <h3>Manager Phone</h3>
                            <p>{{ $branch->manager_phone ?: '-' }}</p>
                        </div>
                        <div class="branch-info-card">
                            <h3>Manager Email</h3>
                            <p>{{ $branch->manager_email ?: '-' }}</p>
                        </div>
                        <div class="branch-info-card">
                            <h3>Branch Phone</h3>
                            <p>{{ $branch->phone ?: '-' }}</p>
                        </div>
                        <div class="branch-info-card">
                            <h3>Address</h3>
                            <p>{{ $branch->address ?: '-' }}</p>
                        </div>
                        <div class="branch-info-card">
                            <h3>Timings</h3>
                            <p>
                                {{ $branch->opening_time && $branch->closing_time ? $branch->opening_time.' - '.$branch->closing_time : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="branch-section">
                    <h2 class="branch-section-title">Manager Identity</h2>
                    <div class="branch-manager">
                        @if($branch->manager_photo)
                            <img
                                src="{{ \App\Support\ImagePath::thumbUrl($branch->manager_photo, 'admin/assets/img/logo.svg') }}"
                                alt="{{ $branch->manager_name ?: $branch->name }}"
                                class="branch-manager-photo"
                            >
                        @else
                            <div class="branch-manager-placeholder">Manager photo not uploaded.</div>
                        @endif

                        <div>
                            <h3 class="branch-manager-name">{{ $branch->manager_name ?: 'Branch Manager' }}</h3>
                            <p class="branch-manager-copy">
                                {{ $branch->manager_email ?: 'Manager email is available from the admin panel.' }}
                                @if($branch->manager_phone)
                                    <br>{{ $branch->manager_phone }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div style="height:18px;"></div>

                    <div class="branch-info-card">
                        <h3>Legal Details</h3>
                        <p>GST No: {{ $branch->gst_no ?: '-' }}</p>
                        <p>FSSAI No: {{ $branch->fssai_no ?: '-' }}</p>
                        <p>Weekly Off: {{ $branch->weekly_off ?: '-' }}</p>
                    </div>

                    <div style="height:18px;"></div>

                    <div class="branch-info-card">
                        <h3>Public Notes</h3>
                        <p>Branch-level details are shown here so customers can quickly verify the location, contacts and compliance info without extra clicks.</p>
                    </div>
                </div>
            </div>

            <div class="branch-section">
                <h2 class="branch-section-title">Branch Reviews</h2>
                <div class="branch-grid">
                    @forelse($branch->reviews->where('status', 'approved')->where('is_published', true) as $review)
                        <div class="review-card">
                            <div class="pill">{{ $review->rating }}/5 Rating</div>
                            <h3>{{ $review->title ?: 'Branch Review' }}</h3>
                            <p class="muted">{{ $review->comment ?: 'Published branch review.' }}</p>
                            <div class="menu-meta">
                                <span class="pill">{{ $review->customer?->name ?: 'Guest Customer' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="branch-info-card">No published branch reviews yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
