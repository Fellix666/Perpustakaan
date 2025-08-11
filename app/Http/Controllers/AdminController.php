<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Menampilkan daftar admin (hanya untuk super admin)
     */
    public function index()
    {
        // Hanya admin dengan role 'admin' yang bisa akses
        if (auth('admin')->user()->role !== 'admin') {
            abort(403, 'Akses hanya untuk Administrator');
        }
        
        $admins = Admin::orderBy('name')->paginate(10);
        return view('admin.index', compact('admins'));
    }

    /**
     * Menampilkan form tambah admin
     */
    public function create()
    {
        if (auth('admin')->user()->role !== 'admin') {
            abort(403, 'Akses hanya untuk Administrator');
        }
        
        return view('admin.create');
    }

    /**
     * Menyimpan admin baru
     */
    public function store(Request $request)
    {
        if (auth('admin')->user()->role !== 'admin') {
            abort(403, 'Akses hanya untuk Administrator');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'kepala_perpus'])],
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.index')
                        ->with('success', 'Admin berhasil ditambahkan');
    }

    /**
     * Menampilkan detail admin
     */
    public function show(Admin $admin)
    {
        if (auth('admin')->user()->role !== 'admin') {
            abort(403, 'Akses hanya untuk Administrator');
        }
        
        return view('admin.show', compact('admin'));
    }

    /**
     * Menampilkan form edit admin
     */
    public function edit(Admin $admin)
    {
        if (auth('admin')->user()->role !== 'admin') {
            abort(403, 'Akses hanya untuk Administrator');
        }
        
        return view('admin.edit', compact('admin'));
    }

    /**
     * Update data admin
     */
    public function update(Request $request, Admin $admin)
    {
        if (auth('admin')->user()->role !== 'admin') {
            abort(403, 'Akses hanya untuk Administrator');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'kepala_perpus'])],
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->role = $request->role;
        
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        
        $admin->save();

        return redirect()->route('admin.index')
                        ->with('success', 'Data admin berhasil diperbarui');
    }

    /**
     * Hapus admin
     */
    public function destroy(Admin $admin)
    {
        if (auth('admin')->user()->role !== 'admin') {
            abort(403, 'Akses hanya untuk Administrator');
        }
        
        // Mencegah admin menghapus dirinya sendiri
        if ($admin->id === auth('admin')->id()) {
            return redirect()->route('admin.index')
                            ->with('error', 'Tidak dapat menghapus akun sendiri');
        }
        
        // Mencegah menghapus admin terakhir
        if (Admin::count() <= 1) {
            return redirect()->route('admin.index')
                            ->with('error', 'Tidak dapat menghapus admin terakhir');
        }
        
        $admin->delete();
        
        return redirect()->route('admin.index')
                        ->with('success', 'Admin berhasil dihapus');
    }

    /**
     * Reset password admin
     */
    public function resetPassword(Admin $admin)
    {
        if (auth('admin')->user()->role !== 'admin') {
            abort(403, 'Akses hanya untuk Administrator');
        }
        
        $newPassword = 'Password123!';
        $admin->password = Hash::make($newPassword);
        $admin->save();
        
        return redirect()->route('admin.index')
                        ->with('success', "Password admin {$admin->name} berhasil direset menjadi: {$newPassword}");
    }

    /**
     * Toggle status aktif/nonaktif admin
     */
    public function toggleStatus(Admin $admin)
    {
        if (auth('admin')->user()->role !== 'admin') {
            abort(403, 'Akses hanya untuk Administrator');
        }
        
        // Mencegah admin menonaktifkan dirinya sendiri
        if ($admin->id === auth('admin')->id()) {
            return redirect()->route('admin.index')
                            ->with('error', 'Tidak dapat menonaktifkan akun sendiri');
        }
        
        $admin->status = $admin->status === 'aktif' ? 'nonaktif' : 'aktif';
        $admin->save();
        
        $statusText = $admin->status === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('admin.index')
                        ->with('success', "Admin {$admin->name} berhasil {$statusText}");
    }
}
