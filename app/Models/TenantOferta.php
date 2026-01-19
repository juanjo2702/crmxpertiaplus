<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantOferta extends Model
{
    protected $table = 'tenant_ofertas';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'tipo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'permanente',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'permanente' => 'boolean',
        'activo' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_oferta', 'oferta_id', 'contact_id');
    }

    // Helper to check if offer is currently valid
    public function isVigente()
    {
        if ($this->permanente) return true;

        $today = now()->toDateString();

        if ($this->fecha_inicio && $this->fecha_fin) {
            return $today >= $this->fecha_inicio && $today <= $this->fecha_fin;
        }

        return $this->activo;
    }
}
