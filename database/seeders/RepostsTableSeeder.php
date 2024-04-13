<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Repost;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class RepostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::enableQueryLog();
        Post::all()->each(function ($post) {
            $usedUserIds = $post->reposts()->pluck('user_id')->toArray();
            $userCount = User::count();
            $repostsToCreate = min(3, $userCount - count($usedUserIds));

            if ($repostsToCreate > 0) {
                $newRepostUsers = User::whereNotIn('id', $usedUserIds)
                                      ->inRandomOrder()
                                      ->take($repostsToCreate)
                                      ->get();

                $newReposts = $newRepostUsers->map(function ($user) use ($post) {
                    return new Repost(['user_id' => $user->id, 'post_id' => $post->id]);
                });

                $post->reposts()->saveMany($newReposts);
            }
        });
    }

}
