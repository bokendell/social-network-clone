<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Post;
use App\Models\Video;
use Illuminate\Support\Facades\Validator;

class VideosController extends Controller
{
    public function getUserVideos(Request $request): JsonResponse
    {
        $videos = $request->user()->videos()->latest();

        return response()->json($videos);
    }

    public function getPostVideos($postID): JsonResponse
    {
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
        $videos = Post::find($postID)->videos()->latest();

        return response()->json($videos);
    }

    public function addVideo($postID): JsonResponse
    {
        $data = array_merge(request()->all(), ['post_id' => $postID]);
        $validator = Validator::make($data, [
            'video_url' => 'required|url',
            'post_id' => 'required|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        if (Post::find($postID)->user_id !== request()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $video = Video::create([
            'video_url' => request()->input('video_url'),
            'user_id' => request()->user()->id,
            'post_id' => $postID,
        ]);

        return response()->json($video);
    }

    public function deleteVideo($postID, $videoID): JsonResponse
    {
        $data = ['post_id' => $postID, 'video_id' => $videoID];
        $validator = Validator::make($data, [
            'post_id' => 'required|exists:posts,id',
            'video_id' => 'required|exists:videos,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $video = Video::find($videoID);
        if ($video->user_id !== request()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $video->delete();

        return response()->json(['message' => 'Video deleted']);
    }

    public function updateVideo($postID, $videoID): JsonResponse
    {
        $data = array_merge(request()->all(), ['post_id' => $postID, 'video_id' => $videoID]);
        $validator = Validator::make($data, [
            'video_url' => 'required|url',
            'post_id' => 'required|exists:posts,id',
            'video_id' => 'required|exists:videos,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $video = Video::find($videoID);
        if ($video->user_id !== request()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $video->update([
            'video_url' => request()->input('video_url')
        ]);

        return response()->json($video);
    }
}
