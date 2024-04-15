<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Post;

class ImagesController extends Controller
{
    public function getUserImages(Request $request): JsonResponse
    {
        $images = $request->user()->images()->paginate(10);

        return response()->json($images);
    }

    public function getPostImages($postID): JsonResponse
    {
        $images = Post::find($postID)->images()->paginate(10);

        return response()->json($images);
    }

    public function addImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image'
        ]);

        $image = $request->user()->images()->create([
            'image' => $request->file('image')->store('images')
        ]);

        return response()->json($image);
    }
    public function deleteImage(Request $request): JsonResponse
    {
        $request->validate([
            'image_id' => 'required|exists:images,id'
        ]);

        $image = $request->user()->images()->find($request->input('image_id'));
        if ($image === null) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        $image->delete();

        return response()->json(['message' => 'Image deleted']);
    }

    public function updateImage(Request $request): JsonResponse
    {
        $request->validate([
            'image_id' => 'required|exists:images,id',
            'image' => 'required|image'
        ]);

        $image = $request->user()->images()->find($request->input('image_id'));
        if ($image === null) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        $image->image = $request->file('image')->store('images');
        $image->save();

        return response()->json($image);
    }
}
