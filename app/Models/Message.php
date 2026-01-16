<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['wam_id', 'contact_id', 'type', 'body', 'status', 'direction', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
