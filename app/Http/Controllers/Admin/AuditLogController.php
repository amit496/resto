<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('audit-logs.view');
        $search = trim((string) $request->string('q'));
        $action = trim((string) $request->string('action'));
        $userId = $request->integer('user_id');

        return view('admin.audit-logs.index', [
            'logs' => AuditLog::query()
                ->with('user')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('action', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('entity_type', 'like', "%{$search}%")
                            ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($action !== '', fn ($query) => $query->where('action', 'like', "%{$action}%"))
                ->when($userId > 0, fn ($query) => $query->where('user_id', $userId))
                ->latest()
                ->paginate(25)
                ->withQueryString(),
            'users' => User::query()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function show(AuditLog $auditLog): View
    {
        Gate::authorize('audit-logs.view');

        return view('admin.audit-logs.show', [
            'log' => $auditLog->load('user'),
        ]);
    }
}

