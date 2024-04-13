<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function accepter()
    {
        return $this->belongsTo(User::class, 'accepter_id');
    }
}
