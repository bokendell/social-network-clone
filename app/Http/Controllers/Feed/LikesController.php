<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\JsonResponse;

class LikesController extends Controller
{
    /**
     * @OA\Get(
     *      path="/feed/posts/{post}/likes",
     *      summary="Get likes for a post.",
     *      tags={"Likes"},
     *      @OA\Response(response=200, description="Post likes", @OA\JsonContent())
     * )
     *
     * Get likes for a post.
     *
     * @param Request $request
     */
    public function getPostLikes($postID) : JsonResponse
    {
        $post = Post::find($postID);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        $likes = $post->likes;
        return response()->json($likes);
    }

    /**
     * Like a post.
     *
     * @param Request $request
     * @param Post $post
     */
    public function likePost($postID) : JsonResponse
    {
        $post = Post::find($postID);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        $like = Like::create([
            'user_id' => request()->user()->id,
            'post_id' => $post->id
        ]);
        return response()->json($like);
    }

    /**
     * Unlike a post.
     *
     * @param Request $request
     * @param Post $post
     */
    public function unlikePost($postID) : JsonResponse
    {
        $post = Post::find($postID);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        $like = Like::where('user_id', request()->user()->id)->where('post_id', $post->id)->first();
        if (!$like) {
            return response()->json(['message' => 'Like not found'], 404);
        }
        Like::destroy($like->id);
        return response()->json(['message' => 'Like removed']);
    }

    /**
     * Get User's liked posts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserLikes(Request $request) : JsonResponse
    {
        $user = $request->user();
        $likedPostsIDs = Like::where('user_id', $user->id)->pluck('post_id');
        $likedPosts = Post::whereIn('id', $likedPostsIDs)->get();
        return response()->json($likedPosts);
    }
}
