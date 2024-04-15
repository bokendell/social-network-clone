<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Post;
use App\Models\Image;
use Illuminate\Support\Facades\Validator;

class ImagesController extends Controller
{
    public function getUserImages(Request $request): JsonResponse
    {
        $images = $request->user()->images();

        return response()->json($images);
    }

    public function getPostImages($postID): JsonResponse
    {
        $validator = Validator::make(['post_id' => $postID], [
            'post_id' => 'required|exists:posts,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $images = Post::find($postID)->images();

        return response()->json($images);
    }

    public function addImage($postID): JsonResponse
    {
        $data = array_merge(request()->all(), ['post_id' => $postID]);
        $validator = Validator::make($data, [
            'image_url' => 'required|url',
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
        $post = Post::find($postID);
        if ($post === null){
            return response()->json(['message' => 'Post does not exist'], 404);
        }
        $image = Image::create([
            'image_url' => request()->input('image_url'),
            'user_id' => request()->user()->id,
            'post_id' => $postID,
        ]);

        return response()->json($image);
    }
    public function deleteImage($postID, $imageID): JsonResponse
    {
        $data = ['post_id' => $postID, 'image_id' => $imageID];
        $validator = Validator::make($data, [
            'post_id' => 'required|exists:posts,id',
            'image_id' => 'required|exists:images,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }

        $image = Image::find($imageID);
        if ($image->user_id !== request()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $image->delete();

        return response()->json(['message' => 'Image deleted']);
    }

    public function updateImage($postID, $imageID): JsonResponse
    {
        $data = array_merge(request()->all(), ['post_id' => $postID, 'image_id' => $imageID]);
        $validator = Validator::make($data, [
            'image_url' => 'required|url',
            'post_id' => 'required|exists:posts,id',
            'image_id' => 'required|exists:images,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }
        $image = Image::find($imageID);
        if ($image->user_id !== request()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $image->update([
            'image_url' => request()->input('image_url')
        ]);

        return response()->json($image);
    }
}
