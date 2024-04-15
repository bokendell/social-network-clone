<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use App\Models\Post;
use Illuminate\Database\Eloquent\Casts\Json;

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
            ->latest();

        return response()->json($posts);
    }

    /**
     * Get a post.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function getPost($postID): JsonResponse {
        $post = Post::find($postID);
        if ($post === null){
            return response()->json(['message' => 'Post does not exist'], 400);
        }
        return response()->json($post);
    }

    /**
     * Store a new post.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createPost(): JsonResponse {
        if (request('content') === null){
            return response()->json(['message' => 'Content cannot be empty'], 400);
        }
        $post = Post::create([
            'user_id' => auth()->id(),
            'content' => request('content')
        ]);
        return response()->json($post);
    }

    /**
     * Delete a post.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function deletePost($postID): JsonResponse {
        $post = Post::find($postID);
        if ($post === null){
            return response()->json(['message' => 'Post does not exist'], 400);
        }
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
     * Update a post.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function updatePost($postID): JsonResponse {
        $post = Post::find($postID);
        if ($post === null){
            return response()->json(['message' => 'Post does not exist'], 400);
        }
        $user = auth()->user();
        if ($post->user_id !== $user->id){
            return response()->json(['message' => 'Post does not belong to user'], 403);
        }
        if (request('content') === null){
            return response()->json(['message' => 'Content cannot be empty'], 400);
        }
        $post->update([
            'content' => request('content')
        ]);
        return response()->json($post);
    }
}
