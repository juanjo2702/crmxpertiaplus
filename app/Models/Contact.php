<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'wa_id',
        'name',
        'email',
        'profile_pic',
        'tenant_id',
        'nivel_interes',
        'estado_crm',
        'notas_crm',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // CRM Relationships
    public function carreras()
    {
        return $this->belongsToMany(TenantCarrera::class, 'contact_carrera', 'contact_id', 'carrera_id');
    }

    public function sedes()
    {
        return $this->belongsToMany(TenantSede::class, 'contact_sede', 'contact_id', 'sede_id');
    }

    public function ofertas()
    {
        return $this->belongsToMany(TenantOferta::class, 'contact_oferta', 'contact_id', 'oferta_id');
    }

    public function eventos()
    {
        return $this->belongsToMany(TenantEvento::class, 'contact_evento', 'contact_id', 'evento_id');
    }
}
