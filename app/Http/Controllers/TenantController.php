<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TenantController extends Controller
{
    /**
     * Display a listing of the tenants.
     */
    public function index()
    {
        $tenants = Tenant::withCount('users')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Tenants/Index', [
            'tenants' => $tenants,
        ]);
    }

    /**
     * Store a newly created tenant in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Tenant data
            'empresa_nombre' => 'required|string|max:255',
            'nit' => 'nullable|string|max:50',
            'razon_social' => 'nullable|string|max:255',
            'empresa_telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:500',
            // Admin user data
            'admin_nombre' => 'required|string|max:255',
            'admin_apellidos' => 'required|string|max:255',
            'admin_ci' => 'required|string|max:20',
            'admin_email' => 'required|email|unique:users,email',
        ]);

        // Generate random password
        $generatedPassword = Str::random(10);

        // Create tenant
        $tenant = Tenant::create([
            'name' => $validated['empresa_nombre'],
            'nit' => $validated['nit'] ?? null,
            'razon_social' => $validated['razon_social'] ?? null,
            'phone' => $validated['empresa_telefono'] ?? null,
            'address' => $validated['direccion'] ?? null,
            'status' => 'active',
        ]);

        // Get tenant_admin role
        $tenantAdminRole = Role::where('name', 'tenant_admin')->first();

        // Create admin user for this tenant
        $adminUser = User::create([
            'name' => $validated['admin_nombre'],
            'apellidos' => $validated['admin_apellidos'],
            'ci' => $validated['admin_ci'],
            'email' => $validated['admin_email'],
            'password' => Hash::make($generatedPassword),
            'tenant_id' => $tenant->id,
            'role_id' => $tenantAdminRole?->id,
        ]);

        return redirect()->back()->with([
            'success' => 'Empresa creada exitosamente.',
            'generated_password' => $generatedPassword,
            'admin_email' => $validated['admin_email'],
        ]);
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load(['users.role']);

        return Inertia::render('Admin/Tenants/Show', [
            'tenant' => $tenant,
        ]);
    }

    /**
     * Update the specified tenant in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nit' => 'nullable|string|max:50',
            'razon_social' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'domain' => 'nullable|string|max:255|unique:tenants,domain,' . $tenant->id,
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $tenant->update($validated);

        return redirect()->back()->with('success', 'Empresa actualizada exitosamente.');
    }

    /**
     * Remove the specified tenant from storage.
     */
    public function destroy(Tenant $tenant)
    {
        // Prevent deleting the system tenant (SOLVEIT System)
        if ($tenant->name === 'SOLVEIT System') {
            return redirect()->back()->with('error', 'No se puede eliminar el tenant del sistema.');
        }

        $tenant->delete();

        return redirect()->back()->with('success', 'Empresa eliminada exitosamente.');
    }
}
