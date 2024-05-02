<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationComment>
 */
class NotificationCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'notification_id' => Notification::factory([
            //     'user'
            // ]),
            // 'comment_id' => Comment::factory(),
        ];
    }
}
