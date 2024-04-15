<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Post;

class VideosController extends Controller
{
    public function getUserVideos(Request $request): JsonResponse
    {
        $videos = $request->user()->videos()->paginate(10);

        return response()->json($videos);
    }

    public function getPostVideos($postID): JsonResponse
    {
        $videos = Post::find($postID)->videos()->paginate(10);

        return response()->json($videos);
    }

    public function addVideo(Request $request): JsonResponse
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4'
        ]);

        $video = $request->user()->videos()->create([
            'video' => $request->file('video')->store('videos')
        ]);

        return response()->json($video);
    }

    public function deleteVideo(Request $request): JsonResponse
    {
        $request->validate([
            'video_id' => 'required|exists:videos,id'
        ]);

        $video = $request->user()->videos()->find($request->input('video_id'));
        if ($video === null) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        $video->delete();

        return response()->json(['message' => 'Video deleted']);
    }

    public function updateVideo(Request $request): JsonResponse
    {
        $request->validate([
            'video_id' => 'required|exists:videos,id',
            'video' => 'required|file|mimes:mp4'
        ]);

        $video = $request->user()->videos()->find($request->input('video_id'));
        if ($video === null) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        $video->video = $request->file('video')->store('videos');
        $video->save();

        return response()->json($video);
    }
}
