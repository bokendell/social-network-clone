<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\RepostResource;
use Illuminate\Http\Request;
use App\Models\Repost;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RepostsController extends Controller
{
    /**
     * @OA\Get(
     *      path="feed/posts/reposts",
     *      summary="Get user reposts.",
     *      tags={"Reposts"},
     *      @OA\Response(response=200, description="User reposts", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Get user reposts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getReposts(Request $request): JsonResponse
    {
        $reposts = Repost::where('user_id', $request->user()->id)->latest();

        return response()->json($reposts);
    }

    public function getUserReposts($userID): JsonResponse
    {
        $data = ['user_id' => $userID,];
        $validator = Validator::make($data, [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $repostsIDs = Repost::where('user_id', $userID)->pluck('post_id');
        $posts = Post::whereIn('id', $repostsIDs)->latest()->get();

        return response()->json([
            'meta' => [
                'total' => $posts->count()
            ],
            'posts' => PostResource::collection($posts)
        ]);
    }

    /**
     * @OA\Get(
     *      path="feed/posts/{post}/reposts",
     *      summary="Get reposts for a post.",
     *      tags={"Reposts"},
     *      @OA\Response(response=200, description="Post reposts", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Get reposts for a post.
     *
     * @param int $postID
     * @return JsonResponse
     */
    public function getPostReposts($postID): JsonResponse
    {
        $data = ['post_id' => $postID,];
        $validator = Validator::make($data, [
            'post_id' => 'required|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $post = Post::find($postID);
        $reposts = Repost::where('post_id', $postID)
            ->with('user')
            ->latest();

        return response()->json($reposts);
    }

    /**
     * @OA\Post(
     *      path="feed/posts/{post}/reposts",
     *      summary="Repost a post.",
     *      tags={"Reposts"},
     *      @OA\Response(response=200, description="Repost", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=403, description="Unauthorized"),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Repost a post.
     *
     * @param int $postID
     * @return JsonResponse
     */
    public function repost($postID): JsonResponse
    {
        $data = ['post_id' => $postID,];
        $validator = Validator::make($data, [
            'post_id' => 'required|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $repost = Repost::where('user_id', auth()->id())
            ->where('post_id', $postID);

        if ($repost->count() > 0){
            return response()->json(['message' => 'Post already reposted'], 422);
        }

        $repost = Repost::create([
            'user_id' => auth()->id(),
            'post_id' => $postID
        ]);

        return response()->json(RepostResource::make($repost));
    }

    /**
     * @OA\Delete(
     *      path="feed/posts/{post}/reposts/{repost}",
     *      summary="Unrepost a post.",
     *      tags={"Reposts"},
     *      @OA\Response(response=200, description="Post unreposted", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=403, description="Unauthorized"),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Unrepost a post.
     *
     * @param int $postID
     * @param int $repostID
     * @return JsonResponse
     */
    public function unrepost($postID, $repostID): JsonResponse
    {
        $data = ['post_id' => $postID, 'repost_id' => $repostID];
        $validator = Validator::make($data, [
            'post_id' => 'required|exists:posts,id',
            'repost_id' => 'required|exists:reposts,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }

        $repost = Repost::find($repostID);
        if ($repost->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $repost->delete();

        return response()->json(['message' => 'Post unreposted']);
    }
}
