<?php

namespace App\Http\Controllers;

use App\Models\TenantSede;
use App\Models\TenantCarrera;
use App\Models\TenantOferta;
use App\Models\TenantEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TenantCatalogController extends Controller
{
    private function getTenantId()
    {
        return Auth::user()->tenant_id;
    }

    // ==================== SEDES ====================
    public function sedes()
    {
        $sedes = TenantSede::where('tenant_id', $this->getTenantId())
            ->withCount('carreras', 'contacts')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Tenant/Catalogs/Sedes', [
            'sedes' => $sedes,
        ]);
    }

    public function storeSede(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'ciudad' => 'nullable|string|max:100',
        ]);

        TenantSede::create([
            ...$validated,
            'tenant_id' => $this->getTenantId(),
        ]);

        return redirect()->back()->with('success', 'Sede creada exitosamente.');
    }

    public function updateSede(Request $request, TenantSede $sede)
    {
        if ($sede->tenant_id !== $this->getTenantId()) abort(403);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'ciudad' => 'nullable|string|max:100',
            'activo' => 'boolean',
        ]);

        $sede->update($validated);
        return redirect()->back()->with('success', 'Sede actualizada.');
    }

    public function destroySede(TenantSede $sede)
    {
        if ($sede->tenant_id !== $this->getTenantId()) abort(403);
        $sede->delete();
        return redirect()->back()->with('success', 'Sede eliminada.');
    }

    // ==================== CARRERAS ====================
    public function carreras()
    {
        $carreras = TenantCarrera::where('tenant_id', $this->getTenantId())
            ->with('sedes')
            ->withCount('contacts')
            ->orderBy('nombre')
            ->get();

        $sedes = TenantSede::where('tenant_id', $this->getTenantId())
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Tenant/Catalogs/Carreras', [
            'carreras' => $carreras,
            'sedes' => $sedes,
        ]);
    }

    public function storeCarrera(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'duracion' => 'nullable|string|max:100',
            'sedes' => 'array',
            'sedes.*' => 'exists:tenant_sedes,id',
        ]);

        $carrera = TenantCarrera::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'duracion' => $validated['duracion'] ?? null,
            'tenant_id' => $this->getTenantId(),
        ]);

        if (!empty($validated['sedes'])) {
            $carrera->sedes()->sync($validated['sedes']);
        }

        return redirect()->back()->with('success', 'Carrera creada exitosamente.');
    }

    public function updateCarrera(Request $request, TenantCarrera $carrera)
    {
        if ($carrera->tenant_id !== $this->getTenantId()) abort(403);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'duracion' => 'nullable|string|max:100',
            'activo' => 'boolean',
            'sedes' => 'array',
            'sedes.*' => 'exists:tenant_sedes,id',
        ]);

        $carrera->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'duracion' => $validated['duracion'] ?? null,
            'activo' => $validated['activo'] ?? true,
        ]);

        if (isset($validated['sedes'])) {
            $carrera->sedes()->sync($validated['sedes']);
        }

        return redirect()->back()->with('success', 'Carrera actualizada.');
    }

    public function destroyCarrera(TenantCarrera $carrera)
    {
        if ($carrera->tenant_id !== $this->getTenantId()) abort(403);
        $carrera->delete();
        return redirect()->back()->with('success', 'Carrera eliminada.');
    }

    // ==================== OFERTAS ====================
    public function ofertas()
    {
        $ofertas = TenantOferta::where('tenant_id', $this->getTenantId())
            ->withCount('contacts')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Tenant/Catalogs/Ofertas', [
            'ofertas' => $ofertas,
        ]);
    }

    public function storeOferta(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'permanente' => 'boolean',
        ]);

        TenantOferta::create([
            ...$validated,
            'tenant_id' => $this->getTenantId(),
        ]);

        return redirect()->back()->with('success', 'Oferta creada exitosamente.');
    }

    public function updateOferta(Request $request, TenantOferta $oferta)
    {
        if ($oferta->tenant_id !== $this->getTenantId()) abort(403);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'permanente' => 'boolean',
            'activo' => 'boolean',
        ]);

        $oferta->update($validated);
        return redirect()->back()->with('success', 'Oferta actualizada.');
    }

    public function destroyOferta(TenantOferta $oferta)
    {
        if ($oferta->tenant_id !== $this->getTenantId()) abort(403);
        $oferta->delete();
        return redirect()->back()->with('success', 'Oferta eliminada.');
    }

    // ==================== EVENTOS ====================
    public function eventos()
    {
        $eventos = TenantEvento::where('tenant_id', $this->getTenantId())
            ->withCount('contacts')
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return Inertia::render('Tenant/Catalogs/Eventos', [
            'eventos' => $eventos,
        ]);
    }

    public function storeEvento(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'lugar' => 'nullable|string|max:255',
        ]);

        TenantEvento::create([
            ...$validated,
            'tenant_id' => $this->getTenantId(),
        ]);

        return redirect()->back()->with('success', 'Evento creado exitosamente.');
    }

    public function updateEvento(Request $request, TenantEvento $evento)
    {
        if ($evento->tenant_id !== $this->getTenantId()) abort(403);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'lugar' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ]);

        $evento->update($validated);
        return redirect()->back()->with('success', 'Evento actualizado.');
    }

    public function destroyEvento(TenantEvento $evento)
    {
        if ($evento->tenant_id !== $this->getTenantId()) abort(403);
        $evento->delete();
        return redirect()->back()->with('success', 'Evento eliminado.');
    }
}
