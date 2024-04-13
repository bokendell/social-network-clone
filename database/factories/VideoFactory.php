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
            "https://www.pexels.com/video/857195/download/?search_query=&tracking_id=",
            "https://www.pexels.com/video/857198/download/?search_query=&tracking_id=",
            "https://www.pexels.com/video/857219/download/?search_query=&tracking_id=",
            "https://cdn.pixabay.com/vimeo/529179863/Footage%20Of%20People%20Walking.mp4",
            "https://cdn.pixabay.com/vimeo/319124007/Drone%20Fly.mp4",
            "https://coverr.co/videos/Waves%20Pier--5c0e75aaccf9f159213849",
            "https://coverr.co/videos/downtown-la-from-day-to-night-jahvFKU2bjz",
            "https://www.pexels.com/video/3161334/download/?search_query=&tracking_id=",
            "https://www.pexels.com/video/4790234/download/?search_query=&tracking_id=",
            "https://www.pexels.com/video/4474031/download/?search_query=&tracking_id="
        ];
        return Arr::random($freeVideoUrls);
    }
}
