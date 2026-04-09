@extends('admin.layout.index')
@section('title', 'Branches')
@section('content')
    <div class="mb-4"><h3 class="mb-0"><i class="fa-solid fa-code-branch me-2"></i>Branches</h3></div>

    <div class="card mb-3"><div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.branches.store') }}">
            @csrf
            <div class="row g-2">
                @if(! $singleRestaurantMode)
                    <div class="col-md-2">
                        <select class="form-control" name="restaurant_id" required>
                            <option value="">Restaurant</option>
                            @foreach($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}" @selected((int) old('restaurant_id', $selectedRestaurantId) === $restaurant->id)>{{ $restaurant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-2"><input class="form-control" name="name" placeholder="Branch Name" required></div>
                <div class="col-md-2"><input class="form-control" name="code" placeholder="Code"></div>
                <div class="col-md-2"><input class="form-control" name="phone" placeholder="Phone"></div>
                <div class="col-md-2"><input class="form-control" name="manager_name" placeholder="Manager Name"></div>
                <div class="col-md-2"><input class="form-control" name="manager_phone" placeholder="Manager Phone"></div>
                <div class="col-md-2"><input class="form-control" type="email" name="manager_email" placeholder="Manager Email"></div>
                <div class="col-md-2"><input class="form-control" type="time" name="opening_time" placeholder="Open"></div>
                <div class="col-md-2"><input class="form-control" type="time" name="closing_time" placeholder="Close"></div>
                <div class="col-md-2">
                    <select class="form-control" name="status">
                        <option value="active">active</option>
                        <option value="inactive">inactive</option>
                    </select>
                </div>
                <div class="col-md-3"><input class="form-control" type="file" name="image" accept="image/*"></div>
                <div class="col-md-3"><input class="form-control" type="file" name="manager_photo" accept="image/*"></div>
                <div class="col-md-2"><input class="form-control" name="gst_no" placeholder="GST No"></div>
                <div class="col-md-2"><input class="form-control" name="fssai_no" placeholder="FSSAI No"></div>
                <div class="col-md-3"><textarea class="form-control" name="address" rows="1" placeholder="Address"></textarea></div>
                <div class="col-md-2"><button class="btn btn-primary w-100">Add Branch</button></div>
            </div>
        </form>
    </div></div>

    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Image</th><th>Branch</th><th>Code</th><th>Phone</th><th>Manager</th><th>Timing</th><th>Status</th><th>Address</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @forelse($branches as $branch)
                <tr>
                    <td>
                        @if($branch->image)
                            <img src="{{ \App\Support\ImagePath::thumbUrl($branch->image, 'admin/assets/img/logo.svg') }}" alt="{{ $branch->name }}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;">
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $branch->name }}</td>
                    <td>{{ $branch->code ?: '-' }}</td>
                    <td>{{ $branch->phone ?: '-' }}</td>
                    <td>
                        <div>{{ $branch->manager_name ?: '-' }}</div>
                        <small class="text-muted">{{ $branch->manager_phone ?: '-' }}</small>
                    </td>
                    <td>{{ $branch->opening_time && $branch->closing_time ? $branch->opening_time.' - '.$branch->closing_time : '-' }}</td>
                    <td>{{ $branch->status->value }}</td>
                    <td>{{ $branch->address ?: '-' }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('admin.branches.show', $branch) }}"><i class="fa-solid fa-eye me-1"></i>View</a>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.branches.edit', $branch) }}"><i class="fa-solid fa-edit me-1"></i>Update</a>
                        <form method="POST" action="{{ route('admin.branches.toggle-status', $branch) }}" class="d-inline-block js-confirm-action" data-confirm-title="Change Branch Status?" data-confirm-text="This will update the branch availability status for operations." data-confirm-button="{{ $branch->status->value === 'active' ? 'Set Inactive' : 'Set Active' }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm {{ $branch->status->value === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                <i class="fa-solid {{ $branch->status->value === 'active' ? 'fa-toggle-off' : 'fa-toggle-on' }} me-1"></i>
                                {{ $branch->status->value === 'active' ? 'Inactive' : 'Active' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.branches.destroy', $branch) }}" class="d-inline-block js-confirm-action" data-confirm-title="Archive Branch?" data-confirm-text="This will set the branch to inactive." data-confirm-button="Archive">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-archive me-1"></i>Archive</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center py-4">No branches found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div><div class="card-footer">{{ $branches->links() }}</div></div>
@endsection

