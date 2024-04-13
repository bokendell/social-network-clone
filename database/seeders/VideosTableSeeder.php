<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Video;
use App\Models\User;
use App\Models\Post;

class VideosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::all()->each(function ($post) {
            $post->images()->saveMany(Video::factory(1)->make([
                'user_id' => $post->user_id,
                'post_id' => $post->id,
            ]));
        });
    }
}
