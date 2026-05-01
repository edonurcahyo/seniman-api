<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Admin; // 🔥 IMPORT MODEL ADMIN
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ============ PELANGGAN AUTH ============
    public function register(Request $request)
    {
        $user = Pelanggan::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'no_hp' => $request->no_hp,
        ]);

        return response()->json($user);
    }

    public function login(Request $request)
    {
        $user = Pelanggan::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message'=>'Login gagal'],401);
        }

        return response()->json([
            'user' => $user
        ]);
    }

    // ============ ADMIN AUTH ============
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return response()->json([
                'message' => 'Admin tidak ditemukan'
            ], 401);
        }

        if (!Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Password salah'
            ], 401);
        }

        // Buat token menggunakan Laravel Sanctum
        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'admin' => [
                'id_admin' => $admin->id_admin,
                'nama' => $admin->nama,
                'email' => $admin->email
            ],
            'token' => $token
        ]);
    }

    public function adminProfile(Request $request)
    {
        $admin = $request->user();
        
        return response()->json([
            'id_admin' => $admin->id_admin,
            'nama' => $admin->nama,
            'email' => $admin->email,
            'created_at' => $admin->created_at
        ]);
    }

    public function adminUpdateProfile(Request $request)
    {
        $admin = $request->user();
        
        $request->validate([
            'nama' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:admin,email,' . $admin->id_admin . ',id_admin',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|string|min:6|confirmed'
        ]);

        $updateData = [];
        
        if ($request->has('nama')) {
            $updateData['nama'] = $request->nama;
        }
        
        if ($request->has('email')) {
            $updateData['email'] = $request->email;
        }
        
        if ($request->has('new_password') && $request->new_password) {
            if (!Hash::check($request->current_password, $admin->password)) {
                return response()->json(['message' => 'Password saat ini salah'], 422);
            }
            $updateData['password'] = bcrypt($request->new_password);
        }
        
        if (!empty($updateData)) {
            $admin->update($updateData);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated',
            'admin' => [
                'id_admin' => $admin->id_admin,
                'nama' => $admin->nama,
                'email' => $admin->email
            ]
        ]);
    }

    public function adminLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}