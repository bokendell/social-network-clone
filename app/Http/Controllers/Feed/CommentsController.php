<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{
    /**
     * @OA\Get(
     *      path="feed/posts/comments",
     *      summary="Get user comments.",
     *      tags={"Comments"},
     *      @OA\Response(response=200, description="User comments", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Get user comments.
     *
     * @return JsonResponse
     */
    public function getUserComments(): JsonResponse
    {
        $user = request()->user();
        $comments = Comment::where('user_id', $user->id)->latest()->get();
        return response()->json($comments);
    }

    /**
     * @OA\Get(
     *      path="feed/posts/{post}/comments",
     *      summary="Get comments for a post.",
     *      tags={"Comments"},
     *      @OA\Response(response=200, description="Post comments", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Get comments for a post.
     *
     * @param int $postID
     * @return JsonResponse
     */
    public function getPostComments($postID): JsonResponse
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
        $comments = Comment::where('post_id',$post->id)->latest()->get();
        return response()->json($comments);
    }

    /**
     * @OA\Post(
     *      path="feed/posts/{post}/comment",
     *      summary="Create a comment for a post.",
     *      tags={"Comments"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"content"},
     *              @OA\Property(property="content", type="string", example="This is a comment.")
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Comment created", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Create a comment for a post.
     *
     * @param int $postID
     * @return JsonResponse
     */
    public function createComment($postID): JsonResponse
    {
        $data = array_merge(request()->all(), ['post_id' => $postID]);
        $validator = Validator::make($data, [
            'content' => 'required|string',
            'post_id' => 'required|int|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $post = Post::find($postID);
        $comment = Comment::create([
            'content' => request()->content,
            'user_id' => request()->user()->id,
            'post_id' => $post->id
        ]);

        return response()->json(CommentResource::make($comment));
    }

    /**
     * @OA\Put(
     *      path="feed/posts/{post}/comments/{comment}",
     *      summary="Update a comment for a post.",
     *      tags={"Comments"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"content"},
     *              @OA\Property(property="content", type="string", example="This is an updated comment.")
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Comment updated", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Update a comment for a post.
     *
     * @param int $postID
     * @param int $commentID
     * @return JsonResponse
     */
    public function updateComment($postID, $commentID): JsonResponse
    {
        $data = array_merge(request()->all(), ['post_id' => $postID, 'comment_id' => $commentID]);
        $validator = Validator::make($data, [
            'content' => 'required|string',
            'post_id' => 'required|int|exists:posts,id',
            'comment_id' => 'required|int|exists:comments,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }

        $comment = Comment::find($commentID);
        $comment->update([
            'content' => request()->content,
        ]);

        return response()->json($comment, 200);
    }

    /**
     * @OA\Delete(
     *      path="feed/posts/{post}/comments/{comment}",
     *      summary="Delete a comment for a post.",
     *      tags={"Comments"},
     *      @OA\Response(response=200, description="Comment deleted", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Unauthorized")
     * )
     *
     * Delete a comment for a post.
     *
     * @param int $postID
     * @param int $commentID
     * @return JsonResponse
     */
    public function deleteComment($postID, $commentID): JsonResponse
    {
        $data = ['post_id' => $postID, 'comment_id' => $commentID];
        $validator = Validator::make($data, [
            'post_id' => 'required|int|exists:posts,id',
            'comment_id' => 'required|int|exists:comments,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $commentUser = Comment::find($commentID)->user_id;
        $currentUser = request()->user()->id;
        if ($commentUser !== $currentUser) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $comment = Comment::find($commentID);

        $comment->delete();

        return response()->json(['message' => 'Comment deleted']);
    }
}
