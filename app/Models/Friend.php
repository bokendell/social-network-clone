<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'accepter_id',
        'status',
    ];



    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function accepter()
    {
        return $this->belongsTo(User::class, 'accepter_id');
    }

    public static function searchFriends($userId, $status)
    {
        $friends = Friend::where(function($query) use ($userId) {
            $query->where('requester_id', $userId)
                ->orWhere('accepter_id', $userId);
        })
        ->where('status', $status)
        ->get();

        return $friends;
    }

    public static function searchFriend($userId, $friendId)
    {
        $friend = Friend::where(function($query) use ($userId, $friendId) {
            $query->where('requester_id', $userId)
                ->where('accepter_id', $friendId);
        })
        ->orWhere(function($query) use ($userId, $friendId) {
            $query->where('requester_id', $friendId)
                ->where('accepter_id', $userId);
        })
        ->first();

        return $friend;
    }
}
