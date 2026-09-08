<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $username = strtolower(trim($request->input('username')));
        $password = $request->input('password');

        if ($username === '12345' && $password === '12345') {
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil! Selamat datang.',
                'token' => 'mock-jwt-token-smartest-student-12345',
                'user' => [
                    'id' => 'USR-1001',
                    'username' => '12345',
                    'name' => 'Ahmad Fulan',
                    'role' => 'STUDENT',
                    'nisn' => '0054819201',
                    'class' => 'XII IPA 1',
                    'madrasah' => 'MAN 1 Kota Malang',
                    'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400',
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'NISN atau kata sandi tidak valid. Gunakan 12345 / 12345',
        ], 401);
    }

    public function me()
    {
        return response()->json([
            'id' => 'USR-1001',
            'username' => '12345',
            'name' => 'Ahmad Fulan',
            'role' => 'STUDENT',
            'nisn' => '0054819201',
            'class' => 'XII IPA 1',
            'madrasah' => 'MAN 1 Kota Malang',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400',
        ]);
    }

    public function logout()
    {
        return response()->json(['message' => 'Logout berhasil.']);
    }

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'oldPassword' => 'required',
            'newPassword' => 'required|min:5',
        ]);

        if ($request->input('oldPassword') !== '12345') {
            return response()->json(['message' => 'Kata sandi lama tidak sesuai.'], 400);
        }

        return response()->json(['message' => 'Kata sandi berhasil diperbarui!']);
    }
}
