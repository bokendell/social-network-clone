<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Post;
use App\Models\Friend;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PostResource;


class PostsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/feed/posts",
     *     summary="Get posts.",
     *     security={{ "apiAuth": {} }},
     *     tags={"Posts"},
     *     @OA\Response(
     *         response=200,
     *         description="Posts",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(
     *                     property="total",
     *                     type="integer"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="posts",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string"),
     *                     @OA\Property(
     *                         property="comments",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(
     *                                 property="user",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="name", type="string"),
     *                                 @OA\Property(property="username", type="string")
     *                             ),
     *                             @OA\Property(property="content", type="string"),
     *                             @OA\Property(property="created_at", type="string"),
     *                             @OA\Property(property="updated_at", type="string")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="likes",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(
     *                                 property="user",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="name", type="string"),
     *                                 @OA\Property(property="username", type="string")
     *                             ),
     *                             @OA\Property(property="created_at", type="string"),
     *                             @OA\Property(property="updated_at", type="string")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="images",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="url", type="string"),
     *                             @OA\Property(property="post", type="integer"),
     *                             @OA\Property(
     *                                 property="user",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="name", type="string"),
     *                                 @OA\Property(property="username", type="string")
     *                             ),
     *                             @OA\Property(property="created_at", type="string"),
     *                             @OA\Property(property="updated_at", type="string")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="videos",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="url", type="string"),
     *                             @OA\Property(property="post", type="integer"),
     *                             @OA\Property(
     *                                 property="user",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="name", type="string"),
     *                                 @OA\Property(property="username", type="string")
     *                             ),
     *                             @OA\Property(property="created_at", type="string"),
     *                             @OA\Property(property="updated_at", type="string")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="reposts",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="post", type="integer"),
     *                             @OA\Property(
     *                                 property="user",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="name", type="string"),
     *                                 @OA\Property(property="username", type="string")
     *                             ),
     *                             @OA\Property(property="created_at", type="string"),
     *                             @OA\Property(property="updated_at", type="string")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     *
     * Get posts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPosts(Request $request): JsonResponse
    {
        $friends = Friend::searchFriends($request->user()->id, 'accepted')
            ->map(function ($friend) use ($request) {
                return $friend->requester_id === $request->user()->id
                    ? $friend->accepter_id
                    : $friend->requester_id;
            });
        $posts = Post::whereIn('user_id', $friends)
            ->orWhere('user_id', $request->user()->id)
            ->with('user')
            ->limit(10)
            ->get();


        return response()->json([
            'meta' => [
                'total' => $posts->count()
            ],
            'posts' => PostResource::collection($posts)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/feed/posts/{user}",
     *     operationId="getUserPosts",
     *     tags={"Posts"},
     *     summary="Retrieve all posts for a specific user",
     *     description="Returns a list of posts created by the specified user, identified by user ID.",
     *     security={{ "apiAuth": {} }},
     *     tags={"Posts"},
     *     @OA\Response(
     *         response=200,
     *         description="Posts",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(
     *                     property="total",
     *                     type="integer"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="posts",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string"),
     *                     @OA\Property(
     *                         property="comments",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(
     *                                 property="user",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="name", type="string"),
     *                                 @OA\Property(property="username", type="string")
     *                             ),
     *                             @OA\Property(property="content", type="string"),
     *                             @OA\Property(property="created_at", type="string"),
     *                             @OA\Property(property="updated_at", type="string")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="likes",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(
     *                                 property="user",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="name", type="string"),
     *                                 @OA\Property(property="username", type="string")
     *                             ),
     *                             @OA\Property(property="created_at", type="string"),
     *                             @OA\Property(property="updated_at", type="string")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="images",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="url", type="string"),
     *                             @OA\Property(property="post", type="integer"),
     *                             @OA\Property(
     *                                 property="user",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="name", type="string"),
     *                                 @OA\Property(property="username", type="string")
     *                             ),
     *                             @OA\Property(property="created_at", type="string"),
     *                             @OA\Property(property="updated_at", type="string")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="videos",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="url", type="string"),
     *                             @OA\Property(property="post", type="integer"),
     *                             @OA\Property(
     *                                 property="user",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="name", type="string"),
     *                                 @OA\Property(property="username", type="string")
     *                             ),
     *                             @OA\Property(property="created_at", type="string"),
     *                             @OA\Property(property="updated_at", type="string")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="reposts",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="post", type="integer"),
     *                             @OA\Property(
     *                                 property="user",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="name", type="string"),
     *                                 @OA\Property(property="username", type="string")
     *                             ),
     *                             @OA\Property(property="created_at", type="string"),
     *                             @OA\Property(property="updated_at", type="string")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid input"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={
     *                     "type": "array",
     *                     "items": {
     *                         "type": "string"
     *                     }
     *                 }
     *             )
     *         )
     *     )
     * )
     *
     * Retrieve all posts for a specific user.
     *
     * @param int $userID
     * @return JsonResponse
     */
    public function getUserPosts($userID): JsonResponse {
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
        $posts = Post::where('user_id', $userID)->get();
        return response()->json([
            'meta' => [
                'total' => $posts->count()
            ],
            'posts' => PostResource::collection($posts)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/feed/posts/{post}",
     *     summary="Get a post.",
     *     security={{ "apiAuth": {} }},
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         description="The ID of the post to retrieve",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer"
     *             ),
     *             @OA\Property(
     *                 property="content",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="username", type="string")
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="comments",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="likes",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="images",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="url", type="string"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="videos",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="url", type="string"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="reposts",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid input"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={
     *                     "type": "array",
     *                     "items": {
     *                         "type": "string"
     *                     }
     *                 }
     *             )
     *         )
     *     ),
     * )
     * Get a post.
     *
     * @param int $postID
     * @return JsonResponse
     */
    public function getPost($postID): JsonResponse {
        $data = ['post_id' => $postID];
        $validator = Validator::make($data, [
            'post_id' => 'required|exists:posts,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $post = Post::find($postID);


        return response()->json(PostResource::make($post));
    }

    /**
     * @OA\Post(
     *     path="/feed/posts",
     *     summary="Create a post.",
     *     tags={"Posts"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *         description="Data for creating a new post",
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(
     *                 property="content",
     *                 type="string",
     *                 description="The content of the post"
     *             ),
     *             @OA\Property(
     *                 property="image_urls",
     *                 type="array",
     *                 description="Array of image URLs",
     *                 @OA\Items(
     *                     type="string",
     *                     format="uri",
     *                     example="http://example.com/image.jpg"
     *                 ),
     *             ),
     *             @OA\Property(
     *                 property="video_urls",
     *                 type="array",
     *                 description="Array of video URLs",
     *                 @OA\Items(
     *                     type="string",
     *                     format="uri",
     *                     example="http://example.com/video.mp4"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="username", type="string")
     *             ),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *             @OA\Property(
     *                 property="comments",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="likes",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="images",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="url", type="string"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="videos",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="url", type="string"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="reposts",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid input"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={
     *                     "type": "array",
     *                     "items": {
     *                         "type": "string"
     *                     }
     *                 }
     *             )
     *         )
     *     ),
     * )
     *
     * Create a post.
     *
     * @return JsonResponse
     */
    public function createPost(): JsonResponse {
        $validator = Validator::make(request()->all(), [
            'content' => 'required|string|max:255',
            'media' => 'nullable|array',
            'media.*.url' => 'sometimes|url',
            'media.*.type' => 'sometimes|in:image,video',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }

        $post = Post::create([
            'user_id' => auth()->id(),
            'content' => request('content')
        ]);

        foreach (request('media', []) as $mediaItem) {
        if ($mediaItem['type'] === 'image') {
            $post->images()->create([
                'image_url' => $mediaItem['url'],
                'user_id' => auth()->id()
            ]);
        } elseif ($mediaItem['type'] === 'video') {
            $post->videos()->create([
                'video_url' => $mediaItem['url'],
                'user_id' => auth()->id()
            ]);
        }
    }

    return response()->json(PostResource::make($post));
}

    /**
     * @OA\Delete(
     *     path="/feed/posts/{postId}",
     *     summary="Delete a post.",
     *     tags={"Posts"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         description="The ID of the post to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Post deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Post deletion failed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Post deletion failed"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Post does not belong to user",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Post does not belong to user"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid input"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={
     *                     "type": "array",
     *                     "items": {
     *                         "type": "string"
     *                     }
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     *
     * Delete a post.
     *
     * @param int $postID
     * @return JsonResponse
     */
    public function deletePost($postID): JsonResponse {
        $data = ['post_id' => $postID];
        $validator = Validator::make($data, [
            'post_id' => 'required|exists:posts,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $post = Post::find($postID);
        $user = auth()->user();
        if ($post->user_id !== $user->id){
            return response()->json(['message' => 'Post does not belong to user'], 403);
        }
        $post->delete();
        if (Post::find($post->id) === null){
            return response()->json(['message' => 'Post deleted successfully'], 200);
        }
        return response()->json(['message' => 'Post deletion failed'], 400);
    }

    /**
     * @OA\Put(
     *     path="/feed/posts/{postId}",
     *     summary="Update a post.",
     *     tags={"Posts"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         description="The ID of the post to update",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Payload for updating a post including content, and optionally image URLs and video URLs",
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(
     *                 property="content",
     *                 type="string",
     *                 description="The new content of the post"
     *             ),
     *             @OA\Property(
     *                 property="image_urls",
     *                 type="string",
     *                 description="Comma-separated string of image URLs to be associated with the post",
     *                 example="http://example.com/image1.jpg,http://example.com/image2.jpg"
     *             ),
     *             @OA\Property(
     *                 property="video_urls",
     *                 type="string",
     *                 description="Comma-separated string of video URLs to be associated with the post",
     *                 example="http://example.com/video1.mp4,http://example.com/video2.mp4"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="username", type="string")
     *             ),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *             @OA\Property(
     *                 property="comments",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="likes",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="images",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="url", type="string"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="videos",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="url", type="string"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="reposts",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="username", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Post does not belong to user",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Post does not belong to user"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid input"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={
     *                     "type": "array",
     *                     "items": {
     *                         "type": "string"
     *                     }
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     * Update a post.
     *
     * @param int $postID
     * @return JsonResponse
     */
    public function updatePost($postID): JsonResponse {
        $data = array_merge(request()->all(), ['post_id' => $postID]);
        $validator = Validator::make($data, [
            'content' => 'required|string',
            'post_id' => 'required|exists:posts,id',
            'image_urls' => 'nullable|string',
            'video_urls' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $post = Post::find($postID);
        $user = auth()->user();
        if ($post->user_id !== $user->id){
            return response()->json(['message' => 'Post does not belong to user'], 403);
        }
        $post->update([
            'content' => request('content')
        ]);
        if (request('image_urls')){
            $image_urls = explode(',', request('image_urls'));
            foreach ($image_urls as $image_url){
                $post->images()->create([
                    'image_url' => $image_url,
                    'user_id' => auth()->id()
                ]);
            }
        }
        if (request('video_urls')){
            $video_urls = explode(',', request('video_urls'));
            foreach ($video_urls as $video_url){
                $post->videos()->create([
                    'video_url' => $video_url,
                    'user_id' => auth()->id()
                ]);
            }
        }
        return response()->json(PostResource::make($post));
    }
}
