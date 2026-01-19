<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'nit', 'razon_social', 'phone', 'address', 'status', 'domain', 'whatsapp_token', 'whatsapp_phone_id', 'whatsapp_business_account_id'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
