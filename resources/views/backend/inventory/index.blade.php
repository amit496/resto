@extends('admin.layout.index')
@section('title', 'Inventory')
@section('content')
    <div class="mb-4"><h3 class="mb-0">Inventory & Stock</h3></div>

    <div class="card mb-3"><div class="card-body">
        <h5 class="mb-3">Create / Update Stock</h5>
        <form method="POST" action="{{ route('admin.inventory.store') }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-3">
                    <select class="form-control" name="restaurant_id">
                        <option value="">Global</option>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" name="product_id" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><input class="form-control" name="current_stock" type="number" step="0.01" placeholder="Current Stock" required></div>
                <div class="col-md-2"><input class="form-control" name="reorder_level" type="number" step="0.01" placeholder="Reorder Level" required></div>
                <div class="col-md-1"><input class="form-control" name="unit" value="pcs" required></div>
                <div class="col-md-1"><button class="btn btn-primary w-100">Save</button></div>
            </div>
        </form>
    </div></div>

    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Product</th><th>Stock</th><th>Reorder</th><th>Unit</th><th>Last Restock</th><th class="text-end">Adjust</th></tr></thead>
            <tbody>
            @forelse($stocks as $stock)
                <tr>
                    <td>{{ $stock->product?->name }}</td>
                    <td>{{ $stock->current_stock }}</td>
                    <td>{{ $stock->reorder_level }}</td>
                    <td>{{ $stock->unit }}</td>
                    <td>{{ optional($stock->last_restocked_at)->format('d M Y H:i') ?: '-' }}</td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('admin.inventory.adjust', $stock) }}" class="d-inline-flex gap-2">
                            @csrf
                            <select name="type" class="form-control form-control-sm">
                                <option value="in">in</option>
                                <option value="out">out</option>
                                <option value="adjustment">adjustment</option>
                            </select>
                            <input class="form-control form-control-sm" name="quantity" type="number" step="0.01" placeholder="Qty" required>
                            <button class="btn btn-sm btn-outline-primary">Apply</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4">No inventory records found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div><div class="card-footer">{{ $stocks->links() }}</div></div>

    <div class="card mt-3"><div class="card-body">
        <h5>Recent Stock Movements</h5>
        <ul class="mb-0">
            @forelse($recentMovements as $movement)
                <li>{{ strtoupper($movement->type) }} - {{ $movement->product?->name }} ({{ $movement->quantity }})</li>
            @empty
                <li>No movement history.</li>
            @endforelse
        </ul>
    </div></div>
@endsection


