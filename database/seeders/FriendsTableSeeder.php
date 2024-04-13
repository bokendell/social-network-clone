<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Friend;
use App\Models\User;

class FriendsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $userIds = $users->pluck('id')->toArray();

        foreach ($users as $user) {
            $potentialFriends = array_diff($userIds, [$user->id]);

            shuffle($potentialFriends);

            for ($i = 0; $i < min(5, count($potentialFriends)); $i++) {
                $friendId = $potentialFriends[$i];

                // Check if the friendship or its reverse already exists
                $friendshipExists = Friend::where(function ($query) use ($user, $friendId) {
                    $query->where('requester_id', $user->id)
                        ->where('accepter_id', $friendId);
                })->orWhere(function ($query) use ($user, $friendId) {
                    $query->where('requester_id', $friendId)
                        ->where('accepter_id', $user->id);
                })->exists();

                if (!$friendshipExists) {
                    Friend::create([
                        'requester_id' => $user->id,
                        'accepter_id' => $friendId,
                        'status' => 'accepted',
                    ]);
                }
            }
        }
    }

}
