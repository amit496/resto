@extends('admin.layout.index')
@section('title', 'Reviews')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0"><i class="fa-solid fa-star-half-stroke me-2"></i>Reviews Management</h3>
    </div>

    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Customer, food, title">
            </div>
            <div class="col-md-3">
                <label class="form-label">Branch</label>
                <select name="branch_id" class="form-control js-filter-select">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected((int) request('branch_id') === $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-control js-filter-select">
                    <option value="">All Status</option>
                    @foreach($statusOptions as $statusOption)
                        <option value="{{ $statusOption }}" @selected(request('status') === $statusOption)>{{ ucfirst($statusOption) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Visibility</label>
                <select name="published" class="form-control js-filter-select">
                    <option value="">All</option>
                    <option value="published" @selected(request('published') === 'published')>Published</option>
                    <option value="hidden" @selected(request('published') === 'hidden')>Hidden</option>
                </select>
            </div>
            <div class="col-md-2 module-filter-actions">
                <button class="btn btn-outline-secondary w-100" type="submit"><i class="fa-solid fa-search me-1"></i>Search</button>
                <a class="btn btn-light w-100" href="{{ route('admin.reviews.index') }}"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Add Food Review</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reviews.food.store') }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6">
                                <select class="form-control js-filter-select" name="product_id" required>
                                    <option value="">Food Item</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control js-filter-select" name="branch_id">
                                    <option value="">Branch (optional)</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control js-filter-select" name="customer_id">
                                    <option value="">Customer (optional)</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2"><input class="form-control" type="number" name="rating" min="1" max="5" placeholder="Rating" required></div>
                            <div class="col-md-4">
                                <select class="form-control" name="status">
                                    @foreach($statusOptions as $statusOption)
                                        <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12"><input class="form-control" name="title" placeholder="Title"></div>
                            <div class="col-md-12"><textarea class="form-control" name="comment" rows="2" placeholder="Review comment"></textarea></div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="food-review-publish">
                                    <label class="form-check-label" for="food-review-publish">Publish now</label>
                                </div>
                            </div>
                            <div class="col-md-12"><button class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Food Review</button></div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Food Reviews</h5></div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Food</th><th>Rating</th><th>Status</th><th>Visibility</th><th class="text-end">Action</th></tr></thead>
                        <tbody>
                        @forelse($foodReviews as $review)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $review->product?->name ?? '-' }}</div>
                                    <small class="text-muted">{{ $review->customer?->name ?? 'Guest' }} | {{ $review->branch?->name ?? 'Any Branch' }}</small>
                                </td>
                                <td>{{ $review->rating }}/5</td>
                                <td>{{ ucfirst($review->status) }}</td>
                                <td>{{ $review->is_published ? 'Published' : 'Hidden' }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.reviews.food.publish', $review) }}" class="d-inline-block js-confirm-action" data-confirm-title="Change Visibility?" data-confirm-text="This will update food review visibility to users." data-confirm-button="{{ $review->is_published ? 'Hide Review' : 'Publish Review' }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm {{ $review->is_published ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                            <i class="fa-solid {{ $review->is_published ? 'fa-eye-slash' : 'fa-eye' }} me-1"></i>{{ $review->is_published ? 'Hide' : 'Publish' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reviews.food.status', $review) }}" class="d-inline-block js-confirm-action" data-confirm-title="Change Review Status?" data-confirm-text="This will update moderation status for this food review." data-confirm-button="Update Status">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $review->status === 'approved' ? 'rejected' : 'approved' }}">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-clipboard-check me-1"></i>{{ $review->status === 'approved' ? 'Reject' : 'Approve' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reviews.food.destroy', $review) }}" class="d-inline-block js-confirm-action" data-confirm-title="Delete Food Review?" data-confirm-text="This action is permanent and will remove this review record." data-confirm-button="Delete">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash me-1"></i>Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4">No food reviews found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $foodReviews->links() }}</div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Add Branch Review</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reviews.branch.store') }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6">
                                <select class="form-control js-filter-select" name="branch_id" required>
                                    <option value="">Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control js-filter-select" name="customer_id">
                                    <option value="">Customer (optional)</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2"><input class="form-control" type="number" name="rating" min="1" max="5" placeholder="Rating" required></div>
                            <div class="col-md-4">
                                <select class="form-control" name="status">
                                    @foreach($statusOptions as $statusOption)
                                        <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12"><input class="form-control" name="title" placeholder="Title"></div>
                            <div class="col-md-12"><textarea class="form-control" name="comment" rows="2" placeholder="Review comment"></textarea></div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="branch-review-publish">
                                    <label class="form-check-label" for="branch-review-publish">Publish now</label>
                                </div>
                            </div>
                            <div class="col-md-12"><button class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Branch Review</button></div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Branch Reviews</h5></div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Branch</th><th>Rating</th><th>Status</th><th>Visibility</th><th class="text-end">Action</th></tr></thead>
                        <tbody>
                        @forelse($branchReviews as $review)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $review->branch?->name ?? '-' }}</div>
                                    <small class="text-muted">{{ $review->customer?->name ?? 'Guest' }}</small>
                                </td>
                                <td>{{ $review->rating }}/5</td>
                                <td>{{ ucfirst($review->status) }}</td>
                                <td>{{ $review->is_published ? 'Published' : 'Hidden' }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.reviews.branch.publish', $review) }}" class="d-inline-block js-confirm-action" data-confirm-title="Change Visibility?" data-confirm-text="This will update branch review visibility to users." data-confirm-button="{{ $review->is_published ? 'Hide Review' : 'Publish Review' }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm {{ $review->is_published ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                            <i class="fa-solid {{ $review->is_published ? 'fa-eye-slash' : 'fa-eye' }} me-1"></i>{{ $review->is_published ? 'Hide' : 'Publish' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reviews.branch.status', $review) }}" class="d-inline-block js-confirm-action" data-confirm-title="Change Review Status?" data-confirm-text="This will update moderation status for this branch review." data-confirm-button="Update Status">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $review->status === 'approved' ? 'rejected' : 'approved' }}">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-clipboard-check me-1"></i>{{ $review->status === 'approved' ? 'Reject' : 'Approve' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reviews.branch.destroy', $review) }}" class="d-inline-block js-confirm-action" data-confirm-title="Delete Branch Review?" data-confirm-text="This action is permanent and will remove this review record." data-confirm-button="Delete">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash me-1"></i>Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4">No branch reviews found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $branchReviews->links() }}</div>
            </div>
        </div>
    </div>
@endsection

