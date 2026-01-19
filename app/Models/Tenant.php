<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'nit', 'razon_social', 'phone', 'address', 'status', 'domain'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
