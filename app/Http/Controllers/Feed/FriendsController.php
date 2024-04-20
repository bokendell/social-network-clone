<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\FriendResource;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\search;

class FriendsController extends Controller
{

    /**
     * @OA\Get(
     *      path="feed/friends",
     *      operationId="getUserFriends",
     *      tags={"Friends"},
     *      summary="Get user friends",
     *      description="Returns user friends",
     *      @OA\Response(
     *          response=200,
     *          description="Got user friends",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     *
     * Get user friends.
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserFriends(Request $request): JsonResponse
    {
        $friends = Friend::searchFriends($request->user()->id, 'accepted');

        return response()->json($friends);
    }


    /**
     * @OA\Get(
     *      path="feed/friends/{userID}",
     *      operationId="getUserFriendsById",
     *      tags={"Friends"},
     *      summary="Get user friends by user ID",
     *      description="Returns user friends by user ID",
     *      @OA\Parameter(
     *          name="userID",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Got user friends",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid input",
     *          @OA\JsonContent()
     *      )
     * )
     *
     * Get user friends by user ID.
     * @param int $userID
     * @return JsonResponse
     */
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

    /**
     * @OA\Post(
     *      path="feed/friends/{userID}",
     *      operationId="sendFriendRequest",
     *      tags={"Friends"},
     *      summary="Send friend request",
     *      description="Send friend request",
     *      @OA\Parameter(
     *          name="userID",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Friend request sent",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid input",
     *          @OA\JsonContent()
     *      )
     * )
     *
     * Send friend request.
     * @param int $userID
     * @return JsonResponse
     */
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
        if ($user_id == $friend_id) {
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
                        'requester_id' => $user_id,
                        'accepter_id' => $friend_id,
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
                        'requester_id' => $user_id,
                        'accepter_id' => $friend_id,
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

    public function followUser($userID): JsonResponse
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
        if ($user_id == $friend_id) {
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
                        'requester_id' => $user_id,
                        'accepter_id' => $friend_id,
                        'status' => 'accepted'
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
                        'requester_id' => $user_id,
                        'accepter_id' => $friend_id,
                        'status' => 'accepted'
                    ]);
                    return response()->json($friend);
                }
            }
        }
        $friend = Friend::create([
            'requester_id' => $user_id,
            'accepter_id' => $friend_id,
            'status' => 'accepted'
        ]);
        return response()->json(FriendResource::make($friend));
    }

    /**
     * @OA\Delete(
     *      path="feed/friends/{userID}",
     *      operationId="removeFriend",
     *      tags={"Friends"},
     *      summary="Remove friend",
     *      description="Remove friend",
     *      @OA\Parameter(
     *          name="userID",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Friend removed",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid input",
     *          @OA\JsonContent()
     *      )
     * )
     *
     * Remove friend.
     * @param int $userID
     * @return JsonResponse
     */
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
        if ($friend === null) {
            return response()->json(['message' => 'Friendship does not exist'], 422);
        }
        if ($friend->status !== 'accepted') {
            return response()->json(['message' => 'Friendship does not exist'], 422);
        }
        $friend->delete();

        return response()->json(['message' => 'Friend removed']);
    }

    /**
     * @OA\Put(
     *      path="feed/friends/{userID}",
     *      operationId="updateFriendship",
     *      tags={"Friends"},
     *      summary="Update friendship",
     *      description="Update friendship",
     *      @OA\Parameter(
     *          name="userID",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"status"},
     *              @OA\Property(property="status", type="string", example="accepted")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Friendship updated",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid input",
     *          @OA\JsonContent()
     *      )
     * )
     *
     * Update friendship.
     * @param int $userID
     * @return JsonResponse
     */
    public function updateFriendship($userID): JsonResponse
    {
        $friend_id = $userID;
        $user_id = request()->user()->id;
        $data = array_merge(request()->all(), ['user_id' => $user_id, 'friend_id' => $friend_id]);
        $validator = Validator::make($data, [
            'friend_id' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:accepted,declined,blocked,pending'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        // check if user id is the same as friend id
        if ($user_id == $friend_id) {
            return response()->json(['message' => 'You cannot update friendship with self'], 422);
        }
        $friend = Friend::searchFriend($user_id, $friend_id);
        if ($friend === null) {
            return response()->json(['message' => 'Friendship does not exist'], 422);
        }
        if ($friend->status === 'blocked' && $friend->requester_id === $user_id) {
            return response()->json(['message' => 'Cannot update user is blocked by requested user'], 422);
        }
        if ($friend->status === 'declined' && $friend->requester_id === $user_id) {
            return response()->json(['message' => 'Cannot update user has declined friend request'], 422);
        }
        if ($friend->status === 'pending' && $friend->requester_id === $user_id) {
            return response()->json(['message' => 'Cannot update user has not accepted friend request'], 422);
        }

        $friend->update([
            'requester_id' => $friend_id,
            'accepter_id' => $user_id,
            'status' => request()->input('status')
        ]);

        return response()->json($friend);
    }

    /**
     * @OA\Get(
     *      path="feed/friends/requests",
     *      operationId="getFriendRequests",
     *      tags={"Friends"},
     *      summary="Get friend requests",
     *      description="Get friend requests",
     *      @OA\Response(
     *          response=200,
     *          description="Got friend requests",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     *
     * Get friend requests.
     * @param Request $request
     * @return JsonResponse
     */
    public function getFriendRequests(Request $request): JsonResponse
    {
        $friendRequests = Friend::searchFriends($request->user()->id, 'pending');
        return response()->json($friendRequests);
    }

    /**
     * @OA\Get(
     *      path="feed/friends/blocked",
     *      operationId="getBlockedFriends",
     *      tags={"Friends"},
     *      summary="Get blocked friends",
     *      description="Get blocked friends",
     *      @OA\Response(
     *          response=200,
     *          description="Got blocked friends",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     *
     * Get blocked friends.
     * @param Request $request
     * @return JsonResponse
     */
    public function getBlockedFriends(Request $request): JsonResponse
    {
        $blockedFriends = Friend::searchFriends($request->user()->id, 'blocked');

        return response()->json($blockedFriends);
    }

    /**
     * @OA\Get(
     *      path="feed/friends/declined",
     *      operationId="getDeclinedFriends",
     *      tags={"Friends"},
     *      summary="Get declined friends",
     *      description="Get declined friends",
     *      @OA\Response(
     *          response=200,
     *          description="Got declined friends",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     *
     * Get declined friends.
     * @param Request $request
     * @return JsonResponse
     */
    public function getDeclinedFriends(Request $request): JsonResponse
    {
        $declinedFriends = Friend::searchFriends($request->user()->id, 'declined');

        return response()->json($declinedFriends);
    }

    /**
     * @OA\Get(
     *      path="feed/friends/pending",
     *      operationId="getPendingFriends",
     *      tags={"Friends"},
     *      summary="Get pending friends",
     *      description="Get pending friends",
     *      @OA\Response(
     *          response=200,
     *          description="Got pending friends",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     *
     * Get pending friends.
     * @param Request $request
     * @return JsonResponse
     */
    public function getPendingFriends(Request $request): JsonResponse
    {
        $pendingFriends = Friend::searchFriends($request->user()->id, 'pending');

        return response()->json($pendingFriends);
    }
}
