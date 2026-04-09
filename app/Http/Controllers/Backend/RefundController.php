<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RefundController extends Controller
{
    public function index(): View
    {
        return view('backend.refunds.index', [
            'refunds' => Refund::query()->with(['order', 'payment'])->latest()->paginate(20),
            'payments' => Payment::query()
                ->where('status', 'paid')
                ->with('order')
                ->latest('paid_at')
                ->limit(100)
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'payment_id' => ['required', 'exists:payments,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string'],
        ]);

        $payment = Payment::query()->findOrFail($data['payment_id']);

        Refund::query()->create([
            'payment_id' => $payment->id,
            'food_order_id' => $payment->food_order_id,
            'amount' => $data['amount'],
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Refund request created.');
    }

    public function updateStatus(Request $request, Refund $refund): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,processed'],
        ]);

        DB::transaction(function () use ($refund, $data) {
            $refund->update([
                'status' => $data['status'],
                'processed_at' => in_array($data['status'], ['approved', 'processed'], true) ? now() : null,
                'processed_by' => auth()->id(),
            ]);

            if ($data['status'] === 'processed') {
                $refund->payment()->update([
                    'status' => 'refunded',
                ]);
            }
        });

        return back()->with('success', 'Refund status updated.');
    }
}

