<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\NotificationComment;
use App\Models\User;
use App\Models\Post;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::all()->pluck('id')->toArray();

        Post::all()->each(function ($post) use ($userIds) {
            $comments = Comment::factory(3)->make()->each(function ($comment) use ($userIds, $post) {
                $comment->user_id = $userIds[array_rand($userIds)];
                $comment->post_id = $post->id;
            });

            $post->comments()->saveMany($comments);

            foreach ($comments as $comment) {
                $notification = Notification::create([
                    'user_id' => $post->user_id,
                    'type' => 'comment',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                NotificationComment::create([
                    'notification_id' => $notification->id,
                    'comment_id' => $comment->id,
                ]);
            }
        });
    }
}
