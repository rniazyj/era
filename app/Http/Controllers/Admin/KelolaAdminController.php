<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class KelolaAdminController extends Controller
{
    public function index(Request $request)
    {
        $admins = User::query()
            ->whereHas('role', fn ($query) => $query->where('nama_role', 'admin_hr'))
            ->with('role')
            ->when($request->q, function ($query, $q) {
                $query->where(function ($search) use ($q) {
                    $search->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('no_telepon', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $eligibleUsers = User::query()
            ->whereHas('role', fn ($query) => $query->where('nama_role', '!=', 'admin_hr'))
            ->with('role')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'no_telepon', 'id_role', 'status_akun']);

        return view('admin.admin.index', compact('admins', 'eligibleUsers'));
    }

    public function create()
    {
        return redirect()->route('admin.admin.index')->with('open_create_admin', true);
    }

    public function store(Request $request)
    {
        $mode = $request->input('mode', 'existing');
        $roleAdmin = Role::query()->where('nama_role', 'admin_hr')->firstOrFail();

        if ($mode === 'existing') {
            $validated = $request->validate([
                'user_id' => ['required', 'integer', 'exists:users,id'],
            ]);

            $admin = User::findOrFail($validated['user_id']);
            abort_if($admin->role && $admin->role->nama_role === 'admin_hr', 422, 'Akun ini sudah merupakan admin.');

            $admin->update([
                'id_role' => $roleAdmin->id_role,
                'status_akun' => 'aktif',
                'percobaan_gagal' => 0,
                'terkunci_sampai' => null,
            ]);

            return redirect()->route('admin.admin.index')->with('success', 'Akun terpilih berhasil dijadikan admin.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'id_role' => $roleAdmin->id_role,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_telepon' => $validated['no_telepon'] ?? null,
            'password' => Hash::make($validated['password']),
            'status_akun' => 'aktif',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.admin.index')->with('success', 'Akun admin baru berhasil ditambahkan.');
    }

    public function edit(User $admin)
    {
        abort_unless($admin->role && $admin->role->nama_role === 'admin_hr', 404);

        return redirect()->route('admin.admin.index')->with('edit_admin_id', $admin->id);
    }

    public function update(Request $request, User $admin)
    {
        abort_unless($admin->role && $admin->role->nama_role === 'admin_hr', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $admin->update($validated);

        return redirect()->route('admin.admin.index')->with('success', 'Data admin berhasil diperbarui.');
    }

    public function destroy(User $admin)
    {
        abort_unless($admin->role && $admin->role->nama_role === 'admin_hr', 404);

        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.admin.index')->with('error', 'Akun admin yang sedang digunakan tidak dapat dicabut haknya.');
        }

        $rolePelamar = Role::query()->where('nama_role', 'pelamar')->firstOrFail();

        $admin->update([
            'id_role' => $rolePelamar->id_role,
            'status_akun' => 'aktif',
        ]);

        return redirect()->route('admin.admin.index')->with('success', 'Hak admin dicabut. Akun tetap tersedia sebagai akun pelamar.');
    }
}
