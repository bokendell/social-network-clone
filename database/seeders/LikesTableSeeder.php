<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Like;
use App\Models\Notification;
use App\Models\NotificationLike;
use App\Models\User;
use App\Models\Post;

class LikesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::all()->each(function ($post) {
            $usedUserIds = $post->likes()->pluck('user_id')->toArray();
            $availableUser = User::whereNotIn('id', $usedUserIds)->inRandomOrder()->first();

            if ($availableUser) {
                $post->likes()->saveMany(Like::factory(1)->make([
                    'user_id' => $availableUser->id,
                    'post_id' => $post->id,
                ]));
                $post->likes->each(function ($like) use ($post){
                    Notification::factory(1)->create([
                        'user_id' => $post->user_id,
                        'type' => 'like',
                    ]);
                    NotificationLike::factory(1)->create([
                        'notification_id' => Notification::latest()->first()->id,
                        'like_id' => $like->id,
                    ]);
                });
            }
        });

    }
}
