<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSede extends Model
{
    protected $table = 'tenant_sedes';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'direccion',
        'telefono',
        'ciudad',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function carreras()
    {
        return $this->belongsToMany(TenantCarrera::class, 'carrera_sede', 'sede_id', 'carrera_id');
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_sede', 'sede_id', 'contact_id');
    }
}
