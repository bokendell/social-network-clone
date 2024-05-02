<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'comment_id',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public static function searchNotificationComment($notificationId, $commentId)
    {
        $notificationComment = NotificationComment::where('notification_id', $notificationId)
            ->where('comment_id', $commentId)
            ->first();

        return $notificationComment;
    }


}
