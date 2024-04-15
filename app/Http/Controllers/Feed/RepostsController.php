<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Repost;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RepostsController extends Controller
{
    public function getUserReposts(Request $request): JsonResponse
    {
        $reposts = Repost::where('user_id', $request->user()->id)->latest();

        return response()->json($reposts);
    }

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
        $repost = Repost::find($postID);

        if ($repost !== null) {
            return response()->json(['message' => 'Post already reposted'], 422);
        }

        $repost = Repost::create([
            'user_id' => auth()->id(),
            'post_id' => $postID
        ]);

        return response()->json($repost);
    }

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
