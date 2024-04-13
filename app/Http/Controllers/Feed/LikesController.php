<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
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
     * @param Post $post
     */
    public function getPostLikes(Post $post) : JsonResponse
    {
        return JsonResponse::create($post->likes);
    }

    /**
     * Like a post.
     *
     * @param Request $request
     * @param Post $post
     */
    public function likePost(Request $request, Post $post) : JsonResponse
    {
        $post->likes()->attach($request->user()->id);
        return JsonResponse::create($post->likes);
    }

    /**
     * Unlike a post.
     *
     * @param Request $request
     * @param Post $post
     */
    public function unlikePost(Request $request, Post $post) : JsonResponse
    {
        $post->likes()->detach($request->user()->id);
        return JsonResponse::create($post->likes);
    }

    /**
     * Get User's liked posts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLikedPosts(Request $request) : JsonResponse
    {
        return JsonResponse::create($request->user()->likes());
    }
}
