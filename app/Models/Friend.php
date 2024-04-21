<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
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

    public static function isBlocked($userId, $friendId): bool
    {
        $friend = Friend::where(function($query) use ($userId, $friendId) {
            $query->where('requester_id', $userId)
                ->where('accepter_id', $friendId);
        })
        ->where('status', 'blocked')
        ->first();

        if ($friend) {
            return true;
        } else {
            return false;
        }
    }

    public static function isFriend($userId, $friendId): bool
    {
        $friend = Friend::where(function($query) use ($userId, $friendId) {
            $query->where('requester_id', $userId)
                ->where('accepter_id', $friendId);
        })
        ->orWhere(function($query) use ($userId, $friendId) {
            $query->where('requester_id', $friendId)
                ->where('accepter_id', $userId);
        })
        ->where('status', 'accepted')
        ->first();

        if ($friend) {
            return true;
        } else {
            return false;
        }
    }

    public static function getRelatedFriends($userAID, $userBID): Collection {
        // Get friends where the user is either requester or accepter
        $userAFriends = Friend::searchFriends($userAID, 'accepted')
            ->pluck('requester_id', 'accepter_id')->flatten()->unique();
        $userBFriends = Friend::searchFriends($userBID, 'accepted')
            ->pluck('requester_id', 'accepter_id')->flatten()->unique();

        // Find common IDs in both friend lists
        $relatedFriendIds = $userAFriends->intersect($userBFriends);

        // Remove UserA's ID from the list of related friend IDs
        $relatedFriendIds = $relatedFriendIds->reject(function ($id) use ($userAID) {
            return $id == $userAID;
        });

        $relatedFriends = User::findMany($relatedFriendIds);

        return $relatedFriends;
    }

}
