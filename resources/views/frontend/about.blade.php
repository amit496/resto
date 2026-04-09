@extends('frontend.layouts.app')

@php
    $brand = $restaurant?->name ?? ($setting->app_name ?? config('app.name', 'Foodhub'));
    $sinceYear = $restaurant?->created_at?->year ?? now()->subYears(3)->year;
@endphp

@section('title', ($setting->app_name ?? config('app.name', 'Foodhub')).' | About Us')

@section('styles')
    <style>
        .about-page {
            --about-orange: #ff8c00;
            --about-ink: #1a1a1a;
            --about-muted: rgba(26, 26, 26, 0.58);
            --about-line: rgba(26, 26, 26, 0.1);
        }
        .about-ribbon {
            background: linear-gradient(180deg, #fff5eb 0%, #fff 100%);
            border-bottom: 1px solid rgba(255, 140, 0, 0.12);
            padding: 14px var(--page-gutter);
            text-align: center;
        }
        .about-ribbon span {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--about-orange);
        }
        .about-hero {
            padding: clamp(40px, 6vw, 72px) var(--page-gutter) clamp(48px, 7vw, 80px);
            text-align: center;
            background: #fff;
        }
        .about-hero .about-hero-inner {
            max-width: min(900px, 100%);
            margin: 0 auto;
        }
        .about-hero h1 {
            margin: 0 0 18px;
            font-family: 'Poppins', 'Outfit', sans-serif;
            font-size: clamp(28px, 4vw, 42px);
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.03em;
            color: var(--about-ink);
        }
        .about-hero p {
            margin: 0;
            font-size: 17px;
            line-height: 1.75;
            color: var(--about-muted);
        }
        .about-pillars {
            padding: 0 var(--page-gutter) 56px;
            background: #fff;
        }
        .about-pillars-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
            width: 100%;
            max-width: none;
            margin: 0 auto;
        }
        .about-pillar-card {
            padding: 28px 26px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid var(--about-line);
            box-shadow: 0 10px 32px rgba(0, 0, 34, 0.05);
        }
        .about-pillar-card h3 {
            margin: 0 0 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 19px;
            font-weight: 700;
            color: var(--about-ink);
        }
        .about-pillar-card p {
            margin: 0;
            font-size: 15px;
            line-height: 1.65;
            color: var(--about-muted);
        }
        .about-branches {
            padding: 56px var(--page-gutter) 64px;
            background: #f7f7f8;
            border-top: 1px solid var(--about-line);
        }
        .about-branches-inner {
            max-width: none;
            width: 100%;
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr);
            gap: clamp(28px, 4vw, 48px);
            align-items: start;
        }
        .about-branches-intro h2 {
            margin: 0 0 14px;
            font-family: 'Poppins', sans-serif;
            font-size: clamp(26px, 3.2vw, 34px);
            font-weight: 700;
            letter-spacing: -0.03em;
            color: var(--about-ink);
        }
        .about-branches-intro p {
            margin: 0;
            font-size: 16px;
            line-height: 1.7;
            color: var(--about-muted);
        }
        .about-branch-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }
        .about-branch-card {
            padding: 20px 20px 18px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid var(--about-line);
            box-shadow: 0 8px 24px rgba(0, 0, 34, 0.04);
        }
        .about-branch-card h3 {
            margin: 0 0 8px;
            font-size: 17px;
            font-weight: 700;
            color: var(--about-ink);
        }
        .about-branch-card .about-branch-address {
            margin: 0 0 14px;
            font-size: 14px;
            line-height: 1.55;
            color: var(--about-muted);
        }
        .about-branch-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .about-pill-manager {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            background: rgba(26, 26, 26, 0.07);
            color: var(--about-ink);
        }
        .about-pill-off {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            background: #fff;
            border: 1px solid var(--about-line);
            color: var(--about-muted);
        }
        .about-testimonials {
            padding: 56px var(--page-gutter) 64px;
            background: #fff;
        }
        .about-testimonials h2 {
            margin: 0 0 28px;
            font-family: 'Poppins', sans-serif;
            font-size: clamp(26px, 3.2vw, 34px);
            font-weight: 700;
            letter-spacing: -0.03em;
            text-align: center;
            color: var(--about-ink);
        }
        .about-testimonial-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
            width: 100%;
            max-width: none;
            margin: 0 auto;
        }
        .about-testimonial-card {
            padding: 24px 22px 20px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid var(--about-line);
            box-shadow: 0 10px 30px rgba(0, 0, 34, 0.05);
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }
        .about-testimonial-card .about-rating-tag {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: var(--about-orange);
            margin-bottom: 12px;
        }
        .about-testimonial-card h3 {
            margin: 0 0 10px;
            font-size: 18px;
            font-weight: 700;
            color: var(--about-ink);
        }
        .about-testimonial-card .about-review-body {
            margin: 0 0 auto;
            padding-bottom: 18px;
            font-size: 15px;
            line-height: 1.65;
            color: var(--about-muted);
            flex: 1;
        }
        .about-testimonial-footer {
            border-top: 1px solid var(--about-line);
            padding-top: 14px;
            margin-top: 4px;
            display: grid;
            gap: 6px;
        }
        .about-testimonial-footer span {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: rgba(26, 26, 26, 0.45);
        }
        .about-empty-note {
            text-align: center;
            padding: 28px;
            color: var(--about-muted);
            font-size: 15px;
            grid-column: 1 / -1;
        }
        .about-section-label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--about-orange);
            margin-bottom: 10px;
        }
        .about-block {
            padding: 56px var(--page-gutter);
        }
        .about-block--muted {
            background: #f7f7f8;
            border-top: 1px solid var(--about-line);
        }
        .about-block-inner {
            max-width: none;
            width: 100%;
            margin: 0 auto;
        }
        .about-block h2 {
            margin: 0 0 16px;
            font-family: 'Poppins', sans-serif;
            font-size: clamp(24px, 3vw, 32px);
            font-weight: 700;
            letter-spacing: -0.03em;
            color: var(--about-ink);
        }
        .about-block .about-lead {
            margin: 0 0 22px;
            font-size: 17px;
            line-height: 1.75;
            color: var(--about-muted);
            max-width: min(56rem, 100%);
        }
        .about-story-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
            gap: 40px;
            align-items: start;
        }
        .about-story-grid p {
            margin: 0 0 16px;
            font-size: 16px;
            line-height: 1.75;
            color: var(--about-muted);
        }
        .about-story-grid p:last-child {
            margin-bottom: 0;
        }
        .about-timeline {
            margin: 0;
            padding: 0;
            list-style: none;
            border-left: 2px solid rgba(255, 140, 0, 0.35);
            padding-left: 22px;
        }
        .about-timeline li {
            position: relative;
            padding-bottom: 22px;
        }
        .about-timeline li:last-child {
            padding-bottom: 0;
        }
        .about-timeline li::before {
            content: "";
            position: absolute;
            left: -29px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--about-orange);
            box-shadow: 0 0 0 4px #fff;
        }
        .about-timeline strong {
            display: block;
            font-size: 15px;
            color: var(--about-ink);
            margin-bottom: 6px;
        }
        .about-timeline span {
            font-size: 14px;
            line-height: 1.6;
            color: var(--about-muted);
        }
        .about-process-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-top: 8px;
        }
        .about-process-step {
            padding: 22px 18px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid var(--about-line);
            text-align: center;
            box-shadow: 0 8px 24px rgba(0, 0, 34, 0.04);
        }
        .about-process-step .step-num {
            width: 36px;
            height: 36px;
            margin: 0 auto 12px;
            border-radius: 10px;
            background: rgba(255, 140, 0, 0.14);
            color: var(--about-orange);
            font-weight: 800;
            font-size: 15px;
            display: grid;
            place-items: center;
        }
        .about-process-step h3 {
            margin: 0 0 8px;
            font-size: 16px;
            font-weight: 700;
            color: var(--about-ink);
        }
        .about-process-step p {
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
            color: var(--about-muted);
        }
        .about-sourcing-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-top: 24px;
        }
        .about-sourcing-card {
            padding: 22px 20px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid var(--about-line);
        }
        .about-sourcing-card h3 {
            margin: 0 0 10px;
            font-size: 16px;
            font-weight: 700;
            color: var(--about-ink);
        }
        .about-sourcing-card p {
            margin: 0;
            font-size: 14px;
            line-height: 1.65;
            color: var(--about-muted);
        }
        .about-team-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
            margin-top: 8px;
        }
        .about-team-card {
            padding: 26px 22px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid var(--about-line);
            box-shadow: 0 10px 28px rgba(0, 0, 34, 0.05);
            text-align: center;
        }
        .about-team-avatar {
            width: 72px;
            height: 72px;
            margin: 0 auto 16px;
            border-radius: 50%;
            background: linear-gradient(145deg, rgba(255, 140, 0, 0.2), rgba(0, 0, 34, 0.06));
            display: grid;
            place-items: center;
            font-size: 28px;
        }
        .about-team-card h3 {
            margin: 0 0 6px;
            font-size: 17px;
            font-weight: 700;
            color: var(--about-ink);
        }
        .about-team-card .role {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--about-orange);
            margin-bottom: 12px;
        }
        .about-team-card p {
            margin: 0;
            font-size: 14px;
            line-height: 1.65;
            color: var(--about-muted);
        }
        .about-closing {
            padding: 48px var(--page-gutter) 56px;
            background: linear-gradient(180deg, #fff9f2 0%, #fff 100%);
            border-top: 1px solid rgba(255, 140, 0, 0.12);
            text-align: center;
        }
        .about-closing h2 {
            max-width: min(48rem, 100%);
            margin: 0 auto 12px;
            font-family: 'Poppins', sans-serif;
            font-size: clamp(22px, 2.8vw, 28px);
            font-weight: 700;
            color: var(--about-ink);
        }
        .about-closing p {
            margin: 0 auto 22px;
            max-width: min(40rem, 100%);
            font-size: 15px;
            color: var(--about-muted);
            line-height: 1.65;
        }
        .about-closing-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            align-items: center;
        }
        @media (max-width: 900px) {
            .about-pillars-grid,
            .about-testimonial-grid {
                grid-template-columns: 1fr;
            }
            .about-branches-inner {
                grid-template-columns: 1fr;
            }
            .about-branch-list {
                grid-template-columns: 1fr;
            }
            .about-story-grid {
                grid-template-columns: 1fr;
            }
            .about-process-grid,
            .about-sourcing-grid,
            .about-team-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="about-page">
        <div class="about-ribbon">
            <span>About the brand</span>
        </div>

        <section class="about-hero">
            <div class="about-hero-inner">
                <h1>Crafting memorable dining experiences, with care and consistency.</h1>
                <p>
                    <strong>{{ $brand }}</strong> brings together thoughtful recipes, disciplined kitchen standards, and warm hospitality.
                    Since we began welcoming guests in <strong>{{ $sinceYear }}</strong>, our focus has remained simple: serve fresh, flavourful food with transparency—whether you dine in, take away, or order online.
                    Every branch reflects the same promise: respect for your time, your palate, and the people who prepare your meal.
                </p>
            </div>
        </section>

        <section class="about-pillars">
            <div class="about-pillars-grid">
                <article class="about-pillar-card">
                    <h3>Our mission</h3>
                    <p>To deliver reliable quality and courteous service at every touchpoint—from menu discovery to checkout—so families and professionals can trust us for everyday meals and celebrations alike.</p>
                </article>
                <article class="about-pillar-card">
                    <h3>Our vision</h3>
                    <p>To grow as a respected restaurant brand with a disciplined branch network: consistent taste, clear communication, and operational excellence that scales without compromising the guest experience.</p>
                </article>
                <article class="about-pillar-card">
                    <h3>Our values</h3>
                    <p>Integrity in ingredients, humility in service, and accountability in operations. We listen to feedback, improve with data, and treat guests, partners, and team members with dignity.</p>
                </article>
            </div>
        </section>

        <section class="about-block about-block--muted">
            <div class="about-block-inner about-story-grid">
                <div>
                    <span class="about-section-label">Our story</span>
                    <h2>From a single kitchen to a trusted neighbourhood name</h2>
                    <p>
                        {{ $brand }} began with a straightforward belief: guests should always know what they are eating, who prepared it, and how it reached their table.
                        What started as focused menu trials and disciplined training in one kitchen gradually evolved into a brand playbook—so every new branch could inherit the same recipes, safety habits, and guest-first etiquette.
                    </p>
                    <p>
                        Today we continue to invest in people and process: structured onboarding for service teams, regular tastings with our culinary leads, and transparent communication when menus or timings change.
                        Our story is still being written—one order, one review, and one satisfied guest at a time.
                    </p>
                </div>
                <div>
                    <ul class="about-timeline">
                        <li>
                            <strong>{{ $sinceYear }} — Foundation</strong>
                            <span>Defined core recipes, hygiene baselines, and the service tone that still guides our front-of-house teams.</span>
                        </li>
                        <li>
                            <strong>Growth &amp; branches</strong>
                            <span>Expanded thoughtfully into new neighbourhoods, pairing local leadership with central standards for menu, pricing, and quality checks.</span>
                        </li>
                        <li>
                            <strong>Digital &amp; delivery</strong>
                            <span>Built a smoother online journey—clear menu imagery, honest descriptions, and reliable order updates—without losing the warmth of an in-store welcome.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="about-block">
            <div class="about-block-inner">
                <span class="about-section-label">How we work</span>
                <h2>From sourcing to your plate—four disciplined steps</h2>
                <p class="about-lead">
                    Consistency is not an accident. These steps describe how we try to earn your trust on every order—whether you are dining in or expecting a punctual delivery.
                </p>
                <div class="about-process-grid">
                    <article class="about-process-step">
                        <div class="step-num">1</div>
                        <h3>Trusted sourcing</h3>
                        <p>We work with vetted suppliers, verify invoices and batch quality, and maintain clear storage rules at each outlet.</p>
                    </article>
                    <article class="about-process-step">
                        <div class="step-num">2</div>
                        <h3>Prep &amp; standards</h3>
                        <p>Recipes are standardised; prep lists and cooking timelines help teams serve hot, fresh food without rushing safety.</p>
                    </article>
                    <article class="about-process-step">
                        <div class="step-num">3</div>
                        <h3>Quality checks</h3>
                        <p>Shift leads monitor presentation, temperature, and packaging—especially for delivery—before an order leaves the pass.</p>
                    </article>
                    <article class="about-process-step">
                        <div class="step-num">4</div>
                        <h3>Guest feedback</h3>
                        <p>Reviews and ratings are read seriously; recurring issues trigger training or menu adjustments rather than excuses.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="about-block about-block--muted">
            <div class="about-block-inner">
                <span class="about-section-label">Sourcing &amp; responsibility</span>
                <h2>Thoughtful choices behind every menu</h2>
                <p class="about-lead">
                    We may not be perfect, but we aim to be honest: about what we serve, how we train our teams, and how we reduce waste where we can.
                </p>
                <div class="about-sourcing-grid">
                    <article class="about-sourcing-card">
                        <h3>Hygiene &amp; compliance</h3>
                        <p>Kitchens follow documented cleaning cycles. Where applicable, we align with FSSAI expectations and keep records for internal audits.</p>
                    </article>
                    <article class="about-sourcing-card">
                        <h3>Local where it makes sense</h3>
                        <p>Seasonal produce and regional favourites are prioritised when quality and reliability meet our standards—supporting growers and fresher taste.</p>
                    </article>
                    <article class="about-sourcing-card">
                        <h3>Waste awareness</h3>
                        <p>We plan prep volumes with sales data, donate where partnerships allow, and continuously coach teams to minimise avoidable spoilage.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="about-branches">
            <div class="about-branches-inner">
                <div class="about-branches-intro">
                    <h2>Branch network</h2>
                    <p>
                        Our outlets are designed to serve neighbourhoods with the same brand standards while honouring local teams and managers.
                        Below you will find active locations, responsible managers where available, and weekly closure information—so you can plan your visit or delivery with confidence.
                    </p>
                    <p style="margin-top:14px;">
                        <a href="{{ route('frontend.branches.index') }}" class="btn" style="margin-top:8px;">View all branches</a>
                    </p>
                </div>
                <div class="about-branch-list">
                    @forelse($branches as $branch)
                        <article class="about-branch-card">
                            <h3>{{ $branch->name }}</h3>
                            <p class="about-branch-address">{{ $branch->address ?: 'Address available on the branches page or upon request at the outlet.' }}</p>
                            <div class="about-branch-meta">
                                @if($branch->manager_name)
                                    <span class="about-pill-manager">{{ $branch->manager_name }}</span>
                                @else
                                    <span class="about-pill-manager">Manager — front desk</span>
                                @endif
                                @if($branch->weekly_off)
                                    <span class="about-pill-off">Weekly off: {{ $branch->weekly_off }}</span>
                                @else
                                    <span class="about-pill-off">Weekly off — contact branch</span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <p class="about-empty-note" style="grid-column:1/-1;">Branch listings will appear here once locations are published from the admin panel.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="about-block">
            <div class="about-block-inner">
                <span class="about-section-label">Leadership</span>
                <h2>People behind the brand</h2>
                <p class="about-lead">
                    {{ $brand }} is steered by experienced operators and culinary leads who believe that culture—not slogans—defines how guests feel when they walk in or open a delivery bag.
                </p>
                <div class="about-team-grid">
                    <article class="about-team-card">
                        <div class="about-team-avatar" aria-hidden="true">⚙</div>
                        <h3>Operations &amp; expansion</h3>
                        <div class="role">Branch excellence</div>
                        <p>Oversees opening playbooks, staffing ratios, and service recovery—so standards stay consistent as we grow.</p>
                    </article>
                    <article class="about-team-card">
                        <div class="about-team-avatar" aria-hidden="true">🍳</div>
                        <h3>Culinary leadership</h3>
                        <div class="role">Menu &amp; quality</div>
                        <p>Guards recipe integrity, trains kitchen teams, and approves seasonal additions only after blind tastings and cost clarity.</p>
                    </article>
                    <article class="about-team-card">
                        <div class="about-team-avatar" aria-hidden="true">🤝</div>
                        <h3>Guest experience</h3>
                        <div class="role">Service culture</div>
                        <p>Shapes training for courtesy, speed, and empathy—online and in-store—using real feedback loops from reviews and surveys.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="about-testimonials">
            <h2>Branch testimonials</h2>
            <div class="about-testimonial-grid">
                @forelse($branchReviews as $review)
                    @php
                        $r = min(5, max(1, (int) $review->rating));
                    @endphp
                    <article class="about-testimonial-card">
                        <div class="about-rating-tag">{{ $r }}/5 rating</div>
                        <h3>{{ $review->title ?: 'Thoughtful service' }}</h3>
                        <p class="about-review-body">{{ $review->comment ?: 'Thank you for sharing your experience with us.' }}</p>
                        <div class="about-testimonial-footer">
                            <span>{{ $review->customer?->name ?: 'Valued guest' }}</span>
                            <span>{{ $review->branch?->name ?: 'Branch team' }}</span>
                        </div>
                    </article>
                @empty
                    <p class="about-empty-note">
                        Guest feedback from branches will appear here after reviews are approved. We invite you to share your experience after your next order.
                    </p>
                @endforelse
            </div>
        </section>

        <section class="about-closing" aria-labelledby="about-closing-heading">
            <h2 id="about-closing-heading">Experience {{ $brand }} your way</h2>
            <p>
                Explore the full menu, discover offers curated for your city, or reach our team if you are planning a larger order or a private gathering.
            </p>
            <div class="about-closing-actions">
                <a href="{{ route('frontend.menu.index') }}" class="btn">Browse menu</a>
                <a href="{{ route('frontend.offers') }}" class="btn-outline">View offers</a>
                <a href="{{ route('frontend.contact') }}" class="btn-ghost">Contact us</a>
            </div>
        </section>
    </div>
@endsection
