<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Repost;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class RepostsController extends Controller
{
    public function getUserReposts(Request $request): JsonResponse
    {
        $reposts = Repost::where('user_id', $request->user()->id)
            ->with('post')
            ->latest()
            ->paginate(10);

        return response()->json($reposts);
    }

    public function getPostReposts($postID): JsonResponse
    {
        $reposts = Repost::where('post_id', $postID)
            ->with('user')
            ->latest()
            ->paginate(10);

        return response()->json($reposts);
    }

    public function repost($postID): JsonResponse
    {
        $post = Post::find($postID);
        if ($post === null) {
            return response()->json(['message' => 'Post does not exist'], 400);
        }

        $repost = Repost::where('user_id', auth()->id())
            ->where('post_id', $postID)
            ->first();

        if ($repost !== null) {
            return response()->json(['message' => 'Post already reposted'], 400);
        }

        $repost = Repost::create([
            'user_id' => auth()->id(),
            'post_id' => $postID
        ]);

        return response()->json($repost);
    }

    public function unrepost($postID): JsonResponse
    {
        $repost = Repost::where('user_id', auth()->id())
            ->where('post_id', $postID)
            ->first();

        if ($repost === null) {
            return response()->json(['message' => 'Post not reposted'], 400);
        }

        $repost->delete();

        return response()->json(['message' => 'Post unreposted']);
    }
}
