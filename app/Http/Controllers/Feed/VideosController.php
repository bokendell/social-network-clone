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
    /**
     * @OA\Get(
     *      path="feed/videos",
     *      summary="Get user videos.",
     *      tags={"Videos"},
     *      @OA\Response(response=200, description="User videos", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Get user videos.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserVideos(Request $request): JsonResponse
    {
        $videos = $request->user()->videos()->latest();

        return response()->json($videos);
    }

    /**
     * @OA\Get(
     *      path="feed/posts/{post}/videos",
     *      summary="Get videos for a post.",
     *      tags={"Videos"},
     *      @OA\Response(response=200, description="Post videos", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Get videos for a post.
     *
     * @param int $postID
     * @return JsonResponse
     */
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

    /**
     * @OA\Post(
     *      path="feed/posts/{post}/videos",
     *      summary="Add a video to a post.",
     *      tags={"Videos"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"video_url"},
     *              @OA\Property(property="video_url", type="string", example="https://www.youtube.com/watch?v=12345")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Video added", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=403, description="Unauthorized")
     * )
     *
     * Add a video to a post.
     *
     * @param int $postID
     * @return JsonResponse
     */
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

    /**
     * @OA\Delete(
     *      path="feed/posts/{post}/videos/{video}",
     *      summary="Delete a video from a post.",
     *      tags={"Videos"},
     *      @OA\Response(response=200, description="Video deleted", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=403, description="Unauthorized")
     * )
     *
     * Delete a video from a post.
     *
     * @param int $postID
     * @param int $videoID
     * @return JsonResponse
     */
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

    /**
     * @OA\Put(
     *      path="feed/posts/{post}/videos/{video}",
     *      summary="Update a video for a post.",
     *      tags={"Videos"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"video_url"},
     *              @OA\Property(property="video_url", type="string", example="https://www.youtube.com/watch?v=12345")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Video updated", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=403, description="Unauthorized")
     * )
     *
     * Update a video for a post.
     *
     * @param int $postID
     * @param int $videoID
     * @return JsonResponse
     */
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
