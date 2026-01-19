<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Contact;
use App\Models\Message;
use Inertia\Inertia;
use Illuminate\Support\Str;

class TenantAdminController extends Controller
{
    /**
     * Display the tenant admin dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $tenantId = $tenant->id;

        // Get real counts
        $totalContacts = Contact::where('tenant_id', $tenantId)->count();

        // Get contact IDs for this tenant
        $contactIds = Contact::where('tenant_id', $tenantId)->pluck('id');

        // Count outgoing messages (sent)
        $totalMessagesSent = Message::whereIn('contact_id', $contactIds)
            ->where('direction', 'outgoing')
            ->count();

        $stats = [
            'total_users' => User::where('tenant_id', $tenantId)->count(),
            'total_contacts' => $totalContacts,
            'total_messages' => $totalMessagesSent,
            'tenant' => $tenant,
        ];

        return Inertia::render('Tenant/Dashboard', [
            'stats' => $stats,
        ]);
    }

    /**
     * Display the users list for this tenant.
     */
    public function users()
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $users = User::with('role')
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Tenant/Users', [
            'users' => $users,
        ]);
    }

    /**
     * Store a new user for this tenant.
     */
    public function storeUser(Request $request)
    {
        $currentUser = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'ci' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'role' => 'required|in:tenant_admin,employee',
        ]);

        // Generate password
        $generatedPassword = Str::random(10);

        // Get the role
        $role = Role::where('name', $validated['role'])->first();

        $newUser = User::create([
            'name' => $validated['name'],
            'apellidos' => $validated['apellidos'],
            'ci' => $validated['ci'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($generatedPassword),
            'tenant_id' => $currentUser->tenant_id,
            'role_id' => $role?->id,
        ]);

        return redirect()->back()->with([
            'success' => 'Usuario creado exitosamente.',
            'generated_password' => $generatedPassword,
            'admin_email' => $validated['email'],
        ]);
    }

    /**
     * Delete a user from this tenant.
     */
    public function destroyUser(User $user)
    {
        $currentUser = Auth::user();

        // Verify the user belongs to the same tenant
        if ($user->tenant_id !== $currentUser->tenant_id) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar este usuario.');
        }

        // Prevent deleting yourself
        if ($user->id === $currentUser->id) {
            return redirect()->back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Display the settings page.
     */
    public function settings()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        return Inertia::render('Tenant/Settings', [
            'tenant' => $tenant,
            'user' => $user,
        ]);
    }

    /**
     * Update password.
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
