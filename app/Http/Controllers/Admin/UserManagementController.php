<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditLogger;
use App\Traits\HandlesImageUploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    use HandlesImageUploads;

    private function ensureAuthorizationCatalog(): void
    {
        foreach (RoleEnum::values() as $role) {
            Role::findOrCreate($role, 'web');
        }

        foreach (PermissionEnum::values() as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
    }

    public function index(Request $request): View
    {
        Gate::authorize('users.view');
        $search = trim((string) $request->string('q'));
        $role = trim((string) $request->string('role'));

        return view('admin.users.index', [
            'users' => User::query()
                ->with('roles', 'permissions')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhereHas('roles', fn ($role) => $role->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($role !== '', fn ($query) => $query->whereHas('roles', fn ($roleQuery) => $roleQuery->where('name', $role)))
                ->latest()
                ->paginate(12)
                ->withQueryString(),
            'roles' => Role::query()->whereIn('name', RoleEnum::values())->orderBy('name')->get(),
        ]);
    }

    public function show(User $user): View
    {
        Gate::authorize('users.view');

        return view('admin.users.show', [
            'user' => $user->load('roles', 'permissions'),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('users.create');
        $this->ensureAuthorizationCatalog();

        return view('admin.users.create', [
            'roles' => Role::query()->whereIn('name', RoleEnum::values())->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('users.create');
        $this->ensureAuthorizationCatalog();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile_image' => ['nullable', 'image', 'max:4096'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ]);

        /** @var UploadedFile|null $profileImage */
        $profileImage = $validated['profile_image'] ?? null;

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'profile_image' => $profileImage instanceof UploadedFile
                ? $this->uploadCompressedImage($profileImage, 'profile')['path']
                : null,
        ]);

        $user->syncRoles($validated['roles'] ?? []);
        $user->syncPermissions($validated['permissions'] ?? []);

        AuditLogger::log(
            action: 'user.created',
            entityType: User::class,
            entityId: $user->id,
            description: "Created user {$user->email}",
            meta: [
                'roles' => $validated['roles'] ?? [],
                'permissions' => $validated['permissions'] ?? [],
            ]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        Gate::authorize('users.edit');
        $this->ensureAuthorizationCatalog();

        return view('admin.users.edit', [
            'user' => $user->load('roles', 'permissions'),
            'roles' => Role::query()->whereIn('name', RoleEnum::values())->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('users.edit');
        $this->ensureAuthorizationCatalog();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_image' => ['nullable', 'image', 'max:4096'],
            'remove_profile_image' => ['nullable', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if (! empty($validated['remove_profile_image']) && $user->profile_image) {
            $this->deleteImageAndThumb($user->profile_image);
            $user->profile_image = null;
        }

        /** @var UploadedFile|null $profileImage */
        $profileImage = $validated['profile_image'] ?? null;
        if ($profileImage instanceof UploadedFile) {
            if ($user->profile_image) {
                $this->deleteImageAndThumb($user->profile_image);
            }

            $user->profile_image = $this->uploadCompressedImage($profileImage, 'profile')['path'];
        }

        $user->save();
        $user->syncRoles($validated['roles'] ?? []);
        $user->syncPermissions($validated['permissions'] ?? []);

        AuditLogger::log(
            action: 'user.updated',
            entityType: User::class,
            entityId: $user->id,
            description: "Updated user {$user->email}",
            meta: [
                'roles' => $validated['roles'] ?? [],
                'permissions' => $validated['permissions'] ?? [],
            ]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('users.delete');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $email = $user->email;
        $id = $user->id;
        $user->delete();

        AuditLogger::log(
            action: 'user.deleted',
            entityType: User::class,
            entityId: $id,
            description: "Deleted user {$email}"
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}

