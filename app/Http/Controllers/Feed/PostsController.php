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
     * @OA\Get(
     *      path="feed/posts",
     *      summary="Get posts.",
     *      tags={"Posts"},
     *      @OA\Response(response=200, description="Posts", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Get posts.
     *
     * @param Request $request
     * @return JsonResponse
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
     * @OA\Get(
     *      path="feed/posts/{post}",
     *      summary="Get a post.",
     *      tags={"Posts"},
     *      @OA\Response(response=200, description="Post", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
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

        return response()->json($post);
    }

    /**
     * @OA\Post(
     *      path="feed/posts",
     *      summary="Create a post.",
     *      tags={"Posts"},
     *      @OA\Response(response=200, description="Post created", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Create a post.
     *
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
     * @OA\Delete(
     *      path="feed/posts/{post}",
     *      summary="Delete a post.",
     *      tags={"Posts"},
     *      @OA\Response(response=200, description="Post deleted", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
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
     *      path="feed/posts/{post}",
     *      summary="Update a post.",
     *      tags={"Posts"},
     *      @OA\Response(response=200, description="Post updated", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
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
