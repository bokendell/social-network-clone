<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentsController extends Controller
{
    public function getUserComments(): JsonResponse
    {
        $user = request()->user();
        $comments = Comment::where('user_id', $user->id)->get();
        return response()->json($comments);
    }

    public function getPostComments($postID): JsonResponse
    {
        $post = Post::find($postID);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        $comments = Comment::where('post_id',$post->id)->get();
        return response()->json($comments);
    }


    public function createComment(): JsonResponse
    {
        $post = Post::find(request()->post_id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        $content = request()->content;
        if (!$content) {
            return response()->json(['message' => 'Content is required'], 400);
        }

        $comment = Comment::create([
            'content' => $content,
            'user_id' => request()->user()->id,
            'post_id' => $post->id
        ]);

        return response()->json($comment);
    }

    public function updateComment($commentID): JsonResponse
    {
        $content =request()->content;
        if (!$content) {
            return response()->json(['message' => 'Content is required'], 400);
        }

        $comment = Comment::find($commentID);
        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $comment->update([
            'content' => $content
        ]);

        return response()->json($comment);
    }

    public function deleteComment($commentID): JsonResponse
    {
        $comment = Comment::find($commentID);
        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }
        $commentUser = Comment::find($commentID)->user_id;
        $currentUser = request()->user()->id;
        if ($commentUser !== $currentUser) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted']);
    }
}
