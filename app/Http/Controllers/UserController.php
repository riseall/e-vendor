<?php

namespace App\Http\Controllers;

use App\Models\mst_anggota;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();
        return view('admin.user.index', compact('users'));
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

    // public function searchInternalUser(Request $request)
    // {
    //     $search = $request->term;

    //     $employees = mst_anggota::where('name', 'LIKE', "%$search%")
    //         ->orWhere('nik', 'LIKE', "%$search%")
    //         ->limit(10)
    //         ->get(['id', 'name', 'nik', 'email']);

    //     return response()->json($employees);
    // }
}
