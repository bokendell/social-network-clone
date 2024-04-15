<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{
    public function getUserComments(): JsonResponse
    {
        $user = request()->user();
        $comments = Comment::where('user_id', $user->id)->latest()->get();
        return response()->json($comments);
    }

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

        return response()->json($comment);
    }

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

        return response()->json($comment);
    }

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
