<?php

namespace Database\Factories;

use App\Models\Like;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationLike>
 */
class NotificationLikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'notification_id' => Notification::factory(),
            // 'like_id' => Like::factory(),
        ];
    }
}
