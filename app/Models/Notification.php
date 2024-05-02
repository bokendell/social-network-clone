<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'seen_at',
        'created_at',
        'updated_at',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notificationComment()
    {
        return $this->hasOne(NotificationComment::class);
    }

    public function notificationLike()
    {
        return $this->hasOne(NotificationLike::class);
    }

    public function notificationRepost()
    {
        return $this->hasOne(NotificationRepost::class);
    }

    public static function searchNotifications($userId)
    {
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return $notifications;
    }

    public static function searchNotificationsByType($userId, $type)
    {
        $notifications = Notification::where('user_id', $userId)
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get();

        return $notifications;
    }

}
