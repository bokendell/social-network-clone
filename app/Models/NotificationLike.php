<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'like_id',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function like()
    {
        return $this->belongsTo(Like::class);
    }

    public static function searchNotificationLike($notificationId, $likeId)
    {
        $notificationLike = NotificationLike::where('notification_id', $notificationId)
            ->where('like_id', $likeId)
            ->first();

        return $notificationLike;
    }
}
