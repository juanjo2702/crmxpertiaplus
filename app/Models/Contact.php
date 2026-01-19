<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['wa_id', 'name', 'email', 'profile_pic', 'tenant_id'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
