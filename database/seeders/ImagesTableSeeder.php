<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Image;
use App\Models\User;
use App\Models\Post;

class ImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::all()->each(function ($post) {
            $post->images()->saveMany(Image::factory(2)->make([
                'user_id' => $post->user_id,
                'post_id' => $post->id,
            ]));
        });
    }
}
