<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;


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
            ->get();

        return response()->json($posts);
    }

    /**
     * Get a post.
     *
     * @param Post $post
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

        return response()->json($post);
    }

    /**
     * Store a new post.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createPost(): JsonResponse {
        $validator = Validator::make(request()->all(), [
            'content' => 'required|string'
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
        return response()->json($post);
    }

    /**
     * Delete a post.
     *
     * @param Post $post
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
     * Update a post.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function updatePost($postID): JsonResponse {
        $data = array_merge(request()->all(), ['post_id' => $postID]);
        $validator = Validator::make($data, [
            'content' => 'required|string',
            'post_id' => 'required|exists:posts,id',
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
        return response()->json($post);
    }
}
