<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'user_id' => User::factory(),
            // 'post_id' => Post::factory(),
            'video_url' => $this->getVideoURL(),
        ];
    }

    private function getVideoURL(): string {
        $freeVideoUrls = [
            "https://www.pexels.com/video/waves-rushing-to-the-shore-1321208/",
            "https://www.pexels.com/video/drone-footage-of-an-island-s-coastline-3859430/",
            "https://www.pexels.com/video/road-trip-4434242/",
            "https://www.pexels.com/video/strong-sea-waves-crashing-the-rocky-shoreline-3226171/",
            "https://www.pexels.com/video/man-on-a-pier-at-a-beautiful-river-5512609/",
        ];
        return Arr::random($freeVideoUrls);
    }
}
