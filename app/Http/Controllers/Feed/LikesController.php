<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Http\Resources\LikeResource;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class LikesController extends Controller
{
    /**
     * @OA\Get(
     *      path="feed/posts/{post}/likes",
     *      summary="Get likes for a post.",
     *      tags={"Likes"},
     *      @OA\Response(response=200, description="Post likes", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Get likes for a post.
     *
     * @param int $postID
     * @return JsonResponse
     */
    public function getPostLikes($postID) : JsonResponse
    {
        $data = ['post_id' => $postID];
        $validator = Validator::make($data, [
            'post_id' => 'required|int|exists:posts,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $post = Post::find($postID);
        $likes = Like::where('post_id', $post->id)->latest()->get();
        return response()->json($likes);
    }

    /**
     * @OA\Post(
     *      path="feed/posts/{post}/likes",
     *      summary="Like a post.",
     *      tags={"Likes"},
     *      @OA\Response(response=200, description="Post liked", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Like a post.
     *
     * @param int $postID
     * @return JsonResponse
     */
    public function likePost($postID) : JsonResponse
    {
        $data = ['post_id' => $postID];
        $validator = Validator::make($data, [
            'post_id' => 'required|int|exists:posts,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }

        $like = Like::where('user_id', auth()->id())
            ->where('post_id', $postID);

        if ($like->count() > 0) {
            return response()->json(['message' => 'Post already liked'], 422);
        }
        $like = Like::create([
            'user_id' => auth()->id(),
            'post_id' => $postID
        ]);
        return response()->json(LikeResource::make($like));
    }

    /**
     * @OA\Delete(
     *      path="feed/posts/{post}/likes",
     *      summary="Unlike a post.",
     *      tags={"Likes"},
     *      @OA\Response(response=200, description="Post unliked", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Unlike a post.
     *
     * @param int postID
     * @return JsonResponse
     */
    public function unlikePost($postID) : JsonResponse
    {
        $data = ['post_id' => $postID];
        $validator = Validator::make($data, [
            'post_id' => 'required|int|exists:posts,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $post = Post::find($postID);
        $like = Like::where('user_id', request()->user()->id)->where('post_id', $post->id)->first();
        if (!$like) {
            return response()->json(['message' => 'Like not found'], 422);
        }
        Like::destroy($like->id);
        return response()->json(['message' => 'Like removed', 'errors']);
    }

    /**
     * @OA\Get(
     *      path="feed/posts/likes",
     *      summary="Get user likes.",
     *      tags={"Likes"},
     *      @OA\Response(response=200, description="User likes", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     * Get User's liked posts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLikes(Request $request) : JsonResponse
    {
        $user = $request->user();
        $likedPostsIDs = Like::where('user_id', $user->id)->pluck('post_id');
        $likedPosts = Post::whereIn('id', $likedPostsIDs)->latest()->get();
        return response()->json($likedPosts);
    }

    public function getUserLikes($userID) : JsonResponse
    {
        $data = ['user_id' => $userID];
        $validator = Validator::make($data, [
            'user_id' => 'required|int|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $likedPostsIDs = Like::where('user_id', $userID)->pluck('post_id');
        $likedPosts = Post::whereIn('id', $likedPostsIDs)->latest()->get();
        return response()->json([
            'meta' => [
                'total' => $likedPosts->count()
            ],
            'posts' => PostResource::collection($likedPosts)
        ]);
    }
}
