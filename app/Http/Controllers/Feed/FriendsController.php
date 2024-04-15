<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Friend;
use App\Models\User;

class FriendsController extends Controller
{
    public function getUserFriends(Request $request): JsonResponse
    {
        $friends = $request->user()->friends()->paginate(10);

        return response()->json($friends);
    }

    public function getUserFriendsById($userID): JsonResponse
    {
        $friends = User::find($userID)->friends()->paginate(10);

        return response()->json($friends);
    }

    public function addFriend($userID): JsonResponse
    {
        $user = User::find($userID);
        if ($user === null) {
            return response()->json(['message' => 'User does not exist'], 400);
        }

        $friend = $request->user()->friends()->where('friend_id', $userID)->first();
        if ($friend !== null) {
            return response()->json(['message' => 'User already added as friend'], 400);
        }

        $friend = Friend::create([
            'user_id' => $request->user()->id,
            'friend_id' => $userID
        ]);

        return response()->json($friend);
    }

    public function removeFriend($userID): JsonResponse
    {
        $friend = $request->user()->friends()->where('friend_id', $userID)->first();
        if ($friend === null) {
            return response()->json(['message' => 'User is not a friend'], 400);
        }

        $friend->delete();

        return response()->json(['message' => 'Friend removed']);
    }

    public function getFriendRequests(Request $request): JsonResponse
    {
        $friendRequests = $request->user()->friendRequests()->paginate(10);

        return response()->json($friendRequests);
    }

    public function updateFriend($userID): JsonResponse
    {
        $friend = $request->user()->friends()->where('friend_id', $userID)->first();
        if ($friend === null) {
            return response()->json(['message' => 'User is not a friend'], 400);
        }

        $friend->update([
            'status' => $request->status
        ]);

        return response()->json($friend);
    }
}
