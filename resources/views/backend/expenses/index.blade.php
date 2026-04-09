@extends('admin.layout.index')
@section('title', 'Expenses')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Expense Manager</h3>
        <span class="badge bg-primary">This Month: {{ number_format((float) $monthExpenseTotal, 2) }}</span>
    </div>

    <div class="card mb-3"><div class="card-body">
        <form method="POST" action="{{ route('admin.expenses.store') }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-2">
                    <select class="form-control" name="restaurant_id">
                        <option value="">Restaurant</option>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><input class="form-control" name="category" placeholder="Category" required></div>
                <div class="col-md-2"><input class="form-control" name="title" placeholder="Title" required></div>
                <div class="col-md-2"><input class="form-control" name="amount" type="number" step="0.01" placeholder="Amount" required></div>
                <div class="col-md-2"><input class="form-control" name="expense_date" type="date" value="{{ now()->toDateString() }}" required></div>
                <div class="col-md-2"><input class="form-control" name="payment_method" placeholder="Method"></div>
                <div class="col-md-12"><textarea class="form-control" name="notes" rows="2" placeholder="Notes"></textarea></div>
                <div class="col-md-2"><button class="btn btn-primary w-100">Add Expense</button></div>
            </div>
        </form>
    </div></div>

    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Date</th><th>Category</th><th>Title</th><th>Amount</th><th>Method</th></tr></thead>
            <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td>{{ optional($expense->expense_date)->format('d M Y') }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>{{ $expense->title }}</td>
                    <td>{{ $expense->amount }}</td>
                    <td>{{ $expense->payment_method ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4">No expenses found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div><div class="card-footer">{{ $expenses->links() }}</div></div>
@endsection


