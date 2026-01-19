<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
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
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255|unique:tenants,domain',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        Tenant::create($validated);

        return redirect()->back()->with('success', 'Empresa creada exitosamente.');
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load('users');

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
