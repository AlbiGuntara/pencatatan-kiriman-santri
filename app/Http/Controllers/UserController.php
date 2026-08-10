<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->with('region:id,name,code')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search'));

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('users/Index', [
            'users' => $users,
            'filters' => $request->only(['search']),
            'roles' => User::ROLES,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('users/Create', [
            'roles' => User::ROLES,
            'regions' => Region::query()->orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'region_iksass_id' => ['nullable', 'required_if:role,iksass_admin', 'exists:regions,id'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'region_iksass_id' => $data['role'] === User::ROLE_IKSASS_ADMIN ? $data['region_iksass_id'] : null,
            'email_verified_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => "Akun {$user->name} berhasil dibuat."]);

        return to_route('users.index');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('users/Edit', [
            'user' => $user,
            'roles' => User::ROLES,
            'regions' => Region::query()->orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'region_iksass_id' => ['nullable', 'required_if:role,iksass_admin', 'exists:regions,id'],
        ]);

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'region_iksass_id' => $data['role'] === User::ROLE_IKSASS_ADMIN ? $data['region_iksass_id'] : null,
        ]);

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => "Akun {$user->name} berhasil diperbarui."]);

        return to_route('users.index');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Akun yang sedang digunakan tidak dapat dihapus.']);

            return back();
        }

        $user->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => "Akun {$user->name} berhasil dihapus."]);

        return to_route('users.index');
    }
}
