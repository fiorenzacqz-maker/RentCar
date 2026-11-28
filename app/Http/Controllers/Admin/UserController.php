<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    private const ROLES = ['admin', 'staff', 'customer'];

    public function index(Request $request): View
    {
        $role = $request->query('role');
        $search = trim((string) $request->query('q'));

        $users = User::query()
            ->when(in_array($role, self::ROLES, true), fn ($q) => $q->where('role', $role))
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search): void {
                $w->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'role', 'search'));
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => null, 'roles' => self::ROLES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'role' => ['required', Rule::in(self::ROLES)],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        User::create($validated); // password otomatis di-hash oleh cast 'hashed'

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user, 'roles' => self::ROLES]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'role' => ['required', Rule::in(self::ROLES)],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ]);

        // Admin tidak boleh menurunkan role dirinya sendiri (mencegah terkunci dari sistem).
        if ($user->id === $request->user()->id && $validated['role'] !== 'admin') {
            return back()->withInput()->withErrors(['role' => 'Anda tidak dapat mengubah role akun Anda sendiri.']);
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->bookings()->exists() || $user->reviews()->exists()) {
            return back()->with('error', 'Akun memiliki riwayat transaksi/ulasan dan tidak dapat dihapus.');
        }

        $user->delete();

        return back()->with('success', 'Akun berhasil dihapus.');
    }
}
