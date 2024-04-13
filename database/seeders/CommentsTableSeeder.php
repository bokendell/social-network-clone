<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\User;
use App\Models\Post;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::all()->each(function ($post) {
            $post->comments()->saveMany(Comment::factory(3)->make([
                'user_id' => User::inRandomOrder()->first()->id,
                'post_id' => $post->id,
            ]));
        });
    }
}
