<?php

namespace App\Http\Controllers;

use App\Models\mst_anggota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        $roles = Role::all();
        return view('admin.user.index', compact('users', 'roles'));
    }

    public function getUser(Request $request)
    {
        $query = User::with(['roles' => function ($q) {
            $q->select('name');
        }]);

        $users = $query->select(['id', 'name', 'username', 'email', 'is_active'])->get();

        return response()->json([
            'data' => $users
        ]);
    }

    public function searchInternalUser(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json([
                'results' => []
            ]);
        }

        try {
            $users = DB::connection('db_master')
                ->table('mst_anggota')
                ->where(function ($query) use ($q) {
                    $query->where('nama', 'like', "%{$q}%")
                        ->orWhere('nik', 'like', "%{$q}%");
                })
                ->select([
                    'nik',
                    'nama',
                    'email',
                    'kode_divisi',
                    'kode_departemen',
                    'ref_nama_jabatan',
                    'ref_nama_departemen',
                    'ref_nama_divisi'
                ])
                ->limit(20)
                ->get();

            // ponytail: auto-map role matching FortifyServiceProvider
            $results = $users->map(function ($item) {
                $suggestedRole = '';
                $kode_divisi = isset($item->kode_divisi) ? trim($item->kode_divisi) : '';
                $kode_departemen = isset($item->kode_departemen) ? trim($item->kode_departemen) : '';

                if ($kode_divisi === 'DV00014') {
                    $suggestedRole = 'Procurement';
                } elseif ($kode_departemen === 'DP00048') {
                    $suggestedRole = 'Quality Assurance';
                } elseif ($kode_departemen === 'DP00009') {
                    $suggestedRole = 'Admin IT';
                }

                $displayText = $item->nama . ' (' . $item->nik . ')';
                if (!empty($item->ref_nama_jabatan)) {
                    $displayText .= ' - ' . $item->ref_nama_jabatan;
                }

                $email = !empty($item->email) ? $item->email : ($item->nik . '@perusahaan.id');

                return [
                    'id' => $item->nik,
                    'text' => $displayText,
                    'nik' => $item->nik,
                    'nama' => $item->nama,
                    'email' => $email,
                    'suggested_role' => $suggestedRole,
                    'jabatan' => isset($item->ref_nama_jabatan) ? $item->ref_nama_jabatan : '',
                    'departemen' => isset($item->ref_nama_departemen) ? $item->ref_nama_departemen : '',
                    'divisi' => isset($item->ref_nama_divisi) ? $item->ref_nama_divisi : ''
                ];
            });

            return response()->json([
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'results' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $userType = $request->input('user_type', 'internal');

        if ($userType === 'internal') {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'role' => 'nullable|string'
            ]);

            // ponytail: Auth karyawan internal dicek langsung ke db_master.mst_anggota di FortifyServiceProvider.
            // Password lokal diisi token acak yang aman.
            $password = Str::random(24);
            $role = $request->input('role');
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            $password = $request->input('password');
            $role = 'Supplier';
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($password),
            'is_active' => 1,
        ]);

        if ($role) {
            $user->syncRoles([$role]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => ($userType === 'internal' ? 'Karyawan internal' : 'Supplier') . ' berhasil ditambahkan.',
                'data' => $user
            ]);
        }

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'nullable|string'
        ]);

        $userData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        $user->update($userData);

        if ($request->has('role')) {
            $user->syncRoles($request->role ? [$request->role] : []);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui.',
                'data' => $user
            ]);
        }

        return redirect()->back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus.'
            ]);
        }

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}
