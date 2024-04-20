<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Friend;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(100)->create();
    }

    public static function createAdmin(): void
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'profile_pic_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
            'bio' => 'admin bio',
            'status' => 'active',
            'dob' =>'2000-01-01',
            'username' => 'admin',
            'password' => Hash::make('admin1234'),
            'remember_token' => Str::random(10)
        ]);
        $adminId = User::where('email', 'admin@admin.com')->first()->id;
        $users = User::all();
        $userIds = $users->pluck('id')->toArray();
        $userIds = array_diff($userIds, [$adminId]);
        foreach ($userIds as $userId) {
            Friend::create([
                'requester_id' => $adminId,
                'accepter_id' => $userId,
                'status' => 'accepted',
            ]);
        }
    }
}
