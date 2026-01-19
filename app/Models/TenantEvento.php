<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantEvento extends Model
{
    protected $table = 'tenant_eventos';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'lugar',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_evento', 'evento_id', 'contact_id');
    }
}
