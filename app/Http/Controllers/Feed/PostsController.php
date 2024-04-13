<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use App\Models\Post;

class PostsController extends Controller
{
    /**
     * Get user's feed.
     *
     * @param Request $request
     * @return Response
     */
    public function getPosts(Request $request): JsonResponse
    {
        $friends = $request->user()->friends()->pluck('id');
        $posts = Post::whereIn('user_id', $friends)
            ->orWhere('user_id', $request->user()->id)
            ->with('user')
            ->latest()
            ->paginate(10);
        return JsonResponse::create($posts);
    }

    /**
     * Store a new post.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createPost(Post $post): JsonResponse {
        $post = Post::create([
            'user_id' => auth()->id(),
            'content' => request('content')
        ]);
        return JsonResponse::create($post);
    }

    /**
     * Delete a post.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function deletePost(Post $post): JsonResponse {
        $post->delete();
        return JsonResponse::create($post);
    }

    /**
     * Update a post.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function updatePost(Post $post): JsonResponse {
        $post->update([
            'content' => request('content')
        ]);
        return JsonResponse::create($post);
    }
}
