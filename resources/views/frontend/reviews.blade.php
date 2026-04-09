@extends('frontend.layouts.app')

@section('title', ($setting->app_name ?? config('app.name', 'FoodiHub')).' | Reviews')

@section('content')
    @php
        $topFoodReview = $foodReviews->first();
        $topBranchReview = $branchReviews->first();
    @endphp

    <section class="page-hero">
        <div class="container home-stack">
            <div class="page-hero-box">
                <div class="eyebrow">Published Reviews</div>
                <h1 class="section-title" style="margin-top:14px;max-width:min(56rem,100%);">Food and branch trust signals from moderated customer feedback.</h1>
                <p class="section-copy" style="max-width:min(48rem,100%);margin:14px 0 0;">Only approved and published reviews appear here, keeping the public frontend aligned with your moderation workflow while still giving the page enough depth to feel production-ready.</p>
            </div>

            <div class="stats-strip">
                <div class="stat-card-lite">
                    <div class="eyebrow">Food Reviews</div>
                    <strong>{{ $foodReviews->total() }}</strong>
                    <span>Approved product reviews currently visible on the frontend.</span>
                </div>
                <div class="stat-card-lite">
                    <div class="eyebrow">Branch Reviews</div>
                    <strong>{{ $branchReviews->total() }}</strong>
                    <span>Moderated branch experience reviews published for visitors.</span>
                </div>
                <div class="stat-card-lite">
                    <div class="eyebrow">Trust Layer</div>
                    <strong>{{ $foodReviews->total() + $branchReviews->total() }}</strong>
                    <span>Total public proof points helping visitors judge quality faster.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section-sm">
        <div class="container">
            @if(auth('customer')->check())
                @php
                    $loggedCustomer = auth('customer')->user();
                    $needsPhone = $loggedCustomer && ! $loggedCustomer->phone;
                @endphp
                <div class="commerce-card" style="margin-bottom: 22px;">
                    <div class="section-title">Submit a Review</div>
                    <div class="grid-2">
                        <form method="POST" action="{{ route('frontend.reviews.food.store') }}" class="panel-card" style="padding:20px;">
                            @csrf
                            <div class="eyebrow" style="margin-bottom: 10px;">Food Review</div>
                            <div class="form-grid">
                                @if ($needsPhone)
                                    <div class="form-field full">
                                        <label for="food_phone">Phone Number</label>
                                        <input id="food_phone" class="input" type="text" name="phone" placeholder="Enter phone for review" required>
                                    </div>
                                @endif
                                <div class="form-field full">
                                    <label for="product_id">Product</label>
                                    <select id="product_id" class="select" name="product_id" required>
                                        <option value="">Select Product</option>
                                        @foreach($reviewProducts as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-field full">
                                    <label for="food_branch_id">Branch (optional)</label>
                                    <select id="food_branch_id" class="select" name="branch_id">
                                        <option value="">Select Branch</option>
                                        @foreach($reviewBranches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-field">
                                    <label for="food_rating">Rating</label>
                                    <input id="food_rating" class="input" type="number" min="1" max="5" name="rating" required>
                                </div>
                                <div class="form-field">
                                    <label for="food_title">Title</label>
                                    <input id="food_title" class="input" type="text" name="title">
                                </div>
                                <div class="form-field full">
                                    <label for="food_comment">Comment</label>
                                    <textarea id="food_comment" class="textarea" name="comment"></textarea>
                                </div>
                            </div>
                            <div class="hero-actions">
                                <button class="btn" type="submit">Submit Food Review</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('frontend.reviews.branch.store') }}" class="panel-card" style="padding:20px;">
                            @csrf
                            <div class="eyebrow" style="margin-bottom: 10px;">Branch Review</div>
                            <div class="form-grid">
                                @if ($needsPhone)
                                    <div class="form-field full">
                                        <label for="branch_phone">Phone Number</label>
                                        <input id="branch_phone" class="input" type="text" name="phone" placeholder="Enter phone for review" required>
                                    </div>
                                @endif
                                <div class="form-field full">
                                    <label for="branch_id">Branch</label>
                                    <select id="branch_id" class="select" name="branch_id" required>
                                        <option value="">Select Branch</option>
                                        @foreach($reviewBranches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-field">
                                    <label for="branch_rating">Rating</label>
                                    <input id="branch_rating" class="input" type="number" min="1" max="5" name="rating" required>
                                </div>
                                <div class="form-field">
                                    <label for="branch_title">Title</label>
                                    <input id="branch_title" class="input" type="text" name="title">
                                </div>
                                <div class="form-field full">
                                    <label for="branch_comment">Comment</label>
                                    <textarea id="branch_comment" class="textarea" name="comment"></textarea>
                                </div>
                            </div>
                            <div class="hero-actions">
                                <button class="btn" type="submit">Submit Branch Review</button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="feature-card" style="margin-bottom: 22px;">
                    <strong>Login required</strong>
                    <p class="muted">Review submit karne ke liye customer account se login karein.</p>
                    <a class="btn-outline" href="{{ route('customer.login') }}">Login</a>
                </div>
            @endif

            <div class="section-head">
                <div>
                    <div class="eyebrow">Food Reviews</div>
                    <h2 class="section-title" style="margin-top:14px;">Real dish feedback, not placeholder blocks.</h2>
                    <p class="section-kicker">Each card surfaces rating, reviewer identity, related branch, and moderated comments in a proper frontend layout.</p>
                </div>
                @if($topFoodReview)
                    <div class="price-tag">{{ $topFoodReview->rating }}/5 Top Rated</div>
                @endif
            </div>

            @if($foodReviews->count() > 0)
                <div class="grid-3">
                    @foreach($foodReviews as $review)
                        <article class="review-grid-card">
                            <div class="review-topline">
                                <span class="rating-badge">{{ $review->rating }}/5 Rating</span>
                                @if($review->product?->name)
                                    <span class="tag">{{ $review->product->name }}</span>
                                @endif
                            </div>

                            <div>
                                <h3>{{ $review->title ?: ($review->product?->name ?? 'Food Review') }}</h3>
                                <p style="margin-top:10px;">{{ $review->comment ?: 'Published customer feedback from the moderation queue.' }}</p>
                            </div>

                            <div class="menu-meta">
                                <span class="pill">{{ $review->customer?->name ?: 'Guest Customer' }}</span>
                                @if($review->branch?->name)
                                    <span class="pill">{{ $review->branch->name }}</span>
                                @endif
                            </div>

                            <div class="review-author">
                                <div>
                                    <strong>{{ $review->customer?->name ?: 'Guest Customer' }}</strong>
                                    <span>{{ $review->branch?->name ?: ($restaurant?->name ?? 'FoodiHub') }}</span>
                                </div>
                                <span class="tag">Moderated</span>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-shell">
                    {{ $foodReviews->links() }}
                </div>
            @else
                <div class="empty-state-card">
                    <h3 class="home-card-title">No food reviews published yet.</h3>
                    <p class="home-card-copy">Approve and publish food reviews from admin, then they will appear here as frontend trust content.</p>
                </div>
            @endif
        </div>
    </section>

    <section class="section-sm">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">Branch Reviews</div>
                    <h2 class="section-title" style="margin-top:14px;">Location-level proof for the brand.</h2>
                    <p class="section-kicker">These reviews make the branch experience feel credible instead of leaving the section as an empty heading.</p>
                </div>
                @if($topBranchReview)
                    <div class="price-tag">{{ $topBranchReview->rating }}/5 Highest</div>
                @endif
            </div>

            @if($branchReviews->count() > 0)
                <div class="grid-3">
                    @foreach($branchReviews as $review)
                        <article class="review-grid-card">
                            <div class="review-topline">
                                <span class="rating-badge">{{ $review->rating }}/5 Rating</span>
                                <span class="tag">{{ $review->branch?->name ?: 'Branch Review' }}</span>
                            </div>

                            <div>
                                <h3>{{ $review->title ?: ($review->branch?->name ?? 'Branch Review') }}</h3>
                                <p style="margin-top:10px;">{{ $review->comment ?: 'Published branch feedback from your moderation workflow.' }}</p>
                            </div>

                            <div class="menu-meta">
                                <span class="pill">{{ $review->customer?->name ?: 'Guest Customer' }}</span>
                                <span class="pill">{{ $review->branch?->name ?: ($restaurant?->name ?? 'Branch') }}</span>
                            </div>

                            <div class="review-author">
                                <div>
                                    <strong>{{ $review->customer?->name ?: 'Guest Customer' }}</strong>
                                    <span>Verified public branch feedback</span>
                                </div>
                                <span class="tag">Published</span>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-shell">
                    {{ $branchReviews->links() }}
                </div>
            @else
                <div class="empty-state-card">
                    <h3 class="home-card-title">No branch reviews published yet.</h3>
                    <p class="home-card-copy">Once approved branch reviews exist, this section will render them in the same card system instead of showing an empty page.</p>
                </div>
            @endif
        </div>
    </section>
@endsection

