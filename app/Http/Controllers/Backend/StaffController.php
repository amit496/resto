<?php

namespace App\Http\Controllers\Backend;

use App\Enums\RoleEnum;
use App\Enums\StaffTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $branchId = $request->integer('branch_id');
        $status = trim((string) $request->string('status'));
        $staffType = trim((string) $request->string('staff_type'));

        return view('backend.staff.index', [
            'staffUsers' => User::query()
                ->with(['roles', 'branch'])
                ->whereHas('roles', fn ($query) => $query->whereIn('name', [RoleEnum::MANAGER->value, RoleEnum::STAFF->value]))
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->when($branchId > 0, fn ($query) => $query->where('branch_id', $branchId))
                ->when($staffType !== '', fn ($query) => $query->where('staff_type', $staffType))
                ->when($status === 'active', fn ($query) => $query->where('is_active', true))
                ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'branches' => Branch::query()->orderBy('name')->get(['id', 'name']),
            'staffRoles' => Role::query()->whereIn('name', [RoleEnum::MANAGER->value, RoleEnum::STAFF->value])->orderBy('name')->get(),
            'staffTypes' => StaffTypeEnum::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'role' => ['required', Rule::in([RoleEnum::MANAGER->value, RoleEnum::STAFF->value])],
            'staff_type' => ['nullable', Rule::in(StaffTypeEnum::values())],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'branch_id' => $data['branch_id'] ?? null,
            'staff_type' => $data['staff_type'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);
        $user->syncRoles([$data['role']]);

        return back()->with('success', 'Staff user created.');
    }

    public function show(User $staff): View
    {
        abort_unless($staff->hasAnyRole([RoleEnum::MANAGER->value, RoleEnum::STAFF->value]), 404);

        return view('backend.staff.show', [
            'staff' => $staff->load(['roles', 'branch']),
            'branches' => Branch::query()->orderBy('name')->get(['id', 'name']),
            'staffRoles' => Role::query()->whereIn('name', [RoleEnum::MANAGER->value, RoleEnum::STAFF->value])->orderBy('name')->get(),
            'staffTypes' => StaffTypeEnum::cases(),
        ]);
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        abort_unless($staff->hasAnyRole([RoleEnum::MANAGER->value, RoleEnum::STAFF->value]), 404);

        $data = $request->validate([
            'phone' => ['nullable', 'string', 'max:30'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'role' => ['required', Rule::in([RoleEnum::MANAGER->value, RoleEnum::STAFF->value])],
            'staff_type' => ['nullable', Rule::in(StaffTypeEnum::values())],
            'is_active' => ['required', 'boolean'],
        ]);

        $staff->update([
            'phone' => $data['phone'] ?? null,
            'branch_id' => $data['branch_id'] ?? null,
            'staff_type' => $data['staff_type'] ?? null,
            'is_active' => (bool) $data['is_active'],
        ]);
        $staff->syncRoles([$data['role']]);

        return back()->with('success', 'Staff user updated.');
    }

    public function toggleStatus(User $staff): RedirectResponse
    {
        abort_unless($staff->hasAnyRole([RoleEnum::MANAGER->value, RoleEnum::STAFF->value]), 404);

        $staff->update([
            'is_active' => ! (bool) $staff->is_active,
        ]);

        return back()->with('success', 'Staff status updated.');
    }

    public function destroy(User $staff): RedirectResponse
    {
        abort_unless($staff->hasAnyRole([RoleEnum::MANAGER->value, RoleEnum::STAFF->value]), 404);

        $staff->update(['is_active' => false]);

        return back()->with('success', 'Staff user archived.');
    }
}

