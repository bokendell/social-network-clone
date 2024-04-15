<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\search;

class FriendsController extends Controller
{

    public function getUserFriends(Request $request): JsonResponse
    {
        $friends = Friend::searchFriends($request->user()->id, 'accepted');

        return response()->json($friends);
    }

    public function getUserFriendsById($userID): JsonResponse
    {
        $data = ['user_id' => $userID];
        $validator = Validator::make($data, [
            'user_id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $friends = Friend::searchFriends($userID, 'accepted');

        return response()->json($friends);
    }

    public function sendFriendRequest($userID): JsonResponse
    {
        $friend_id = $userID;
        $user_id = request()->user()->id;
        $data = ['user_id' => $user_id, 'friend_id' => $friend_id];
        $validator = Validator::make($data, [
            'friend_id' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        // check if user id is the same as friend id
        if ($user_id === $friend_id) {
            return response()->json(['message' => 'You cannot send a friend request to yourself'], 422);
        }
        // check if friendship already exists
        $friend = Friend::searchFriend($user_id, $friend_id);
        if ($friend !== null) {
            if ($friend->status === 'accepted') {
                return response()->json(['message' => 'User is already a friend'], 422);
            }
            else if ($friend->status === 'pending') {
                if ($friend->requester_id === $user_id) {
                    return response()->json(['message' => 'Friend request already sent'], 422);
                }
                else {
                    return response()->json(['message' => 'Already received friend request from requested user'], 422);
                }
            }
            else if ($friend->status === 'blocked') {
                if ($friend->requester_id === $user_id) {
                    return response()->json(['message' => 'Requested user has blocked you'], 422);
                }
                else {
                    $friend->update([
                        'status' => 'pending'
                    ]);
                    return response()->json($friend);
                }
            }
            else if ($friend->status === 'declined') {
                if ($friend->requester_id === $user_id) {
                    return response()->json(['message' => 'Friend request already declined'], 422);
                }
                else {
                    $friend->update([
                        'status' => 'pending'
                    ]);
                    return response()->json($friend);
                }
            }
        }
        $friend = Friend::create([
            'requester_id' => $user_id,
            'accepter_id' => $friend_id,
            'status' => 'pending'
        ]);
        return response()->json($friend);
    }

    public function removeFriend($userID): JsonResponse
    {
        $friend_id = $userID;
        $user_id = request()->user()->id;
        $data = ['user_id' => $user_id, 'friend_id' => $friend_id];
        $validator = Validator::make($data, [
            'friend_id' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        // check if user id is the same as friend id
        if ($user_id === $friend_id) {
            return response()->json(['message' => 'You cannot remove yourself as a friend'], 422);
        }

        $friend = Friend::searchFriend($user_id, $friend_id);
        $friend->delete();

        return response()->json(['message' => 'Friend removed']);
    }

    public function updateFriendship($userID): JsonResponse
    {
        $friend_id = $userID;
        $user_id = request()->user()->id;
        $data = ['user_id' => $user_id, 'friend_id' => $friend_id];
        $validator = Validator::make($data, [
            'friend_id' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        // check if user id is the same as friend id
        if ($user_id === $friend_id) {
            return response()->json(['message' => 'You cannot update friendship with self'], 422);
        }
        $friend = Friend::searchFriend($user_id, $friend_id);
        if ($friend === null) {
            return response()->json(['message' => 'Friendship does not exist'], 400);
        }

        $friend->update([
            'status' => request()->input('status')
        ]);

        return response()->json($friend);
    }

    public function getFriendRequests(Request $request): JsonResponse
    {
        $friendRequests = Friend::searchFriends($request->user()->id, 'pending');
        return response()->json($friendRequests);
    }

    public function getBlockedFriends(Request $request): JsonResponse
    {
        $blockedFriends = Friend::searchFriends($request->user()->id, 'blocked');

        return response()->json($blockedFriends);
    }

    public function getDeclinedFriends(Request $request): JsonResponse
    {
        $declinedFriends = Friend::searchFriends($request->user()->id, 'declined');

        return response()->json($declinedFriends);
    }
}
