<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantCarrera extends Model
{
    protected $table = 'tenant_carreras';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'descripcion',
        'duracion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function sedes()
    {
        return $this->belongsToMany(TenantSede::class, 'carrera_sede', 'carrera_id', 'sede_id');
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_carrera', 'carrera_id', 'contact_id');
    }
}
