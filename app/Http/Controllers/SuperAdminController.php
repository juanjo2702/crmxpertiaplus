<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_tenants' => Tenant::where('name', '!=', 'SOLVEIT System')->count(),
            'total_users' => User::count(),
            'recent_tenants' => Tenant::where('name', '!=', 'SOLVEIT System')->latest()->take(5)->get(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
        ]);
    }

    /**
     * Show profile page
     */
    public function profile()
    {
        return Inertia::render('Admin/Profile');
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'ci' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Perfil actualizado exitosamente.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', 'Contraseña actualizada exitosamente.');
    }
}
