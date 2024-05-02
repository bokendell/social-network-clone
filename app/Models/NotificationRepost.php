<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationRepost extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'repost_id',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function repost()
    {
        return $this->belongsTo(Repost::class);
    }

    public static function searchNotificationRepost($notificationId, $repostId)
    {
        $notificationRepost = NotificationRepost::where('notification_id', $notificationId)
            ->where('repost_id', $repostId)
            ->first();

        return $notificationRepost;
    }
}
