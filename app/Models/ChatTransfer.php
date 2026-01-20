<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatTransfer extends Model
{
    protected $fillable = [
        'contact_id',
        'from_user_id',
        'to_user_id',
        'notes',
        'seen_by_recipient',
    ];

    protected $casts = [
        'seen_by_recipient' => 'boolean',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
