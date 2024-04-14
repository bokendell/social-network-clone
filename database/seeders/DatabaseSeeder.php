<?php

namespace Database\Seeders;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            PostsTableSeeder::class,
            CommentsTableSeeder::class,
            LikesTableSeeder::class,
            ImagesTableSeeder::class,
            VideosTableSeeder::class,
            RepostsTableSeeder::class,
            FriendsTableSeeder::class,
        ]);
        UsersTableSeeder::createAdmin();
    }
}
