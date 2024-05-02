<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\Repost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationRepost>
 */
class NotificationRepostFactory extends Factory
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
            // 'repost_id' => Repost::factory(),
        ];
    }
}
