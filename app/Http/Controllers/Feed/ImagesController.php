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
    /**
     * @OA\Get(
     *      path="feed/images",
     *      summary="Get user images.",
     *      tags={"Images"},
     *      @OA\Response(response=200, description="User images", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Get user images.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserImages(Request $request): JsonResponse
    {
        $images = $request->user()->images();

        return response()->json($images);
    }

    /**
     * @OA\Get(
     *      path="feed/posts/{post}/images",
     *      summary="Get images for a post.",
     *      tags={"Images"},
     *      @OA\Response(response=200, description="Post images", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * Get images for a post.
     *
     * @param int $postID
     * @return JsonResponse
     */
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
        $images = Post::find($postID)->images()->latest();

        return response()->json($images);
    }

    /**
     * @OA\Post(
     *      path="feed/posts/{post}/images",
     *      summary="Add an image to a post.",
     *      tags={"Images"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"image_url"},
     *              @OA\Property(property="image_url", type="string", format="url", example="https://example.com/image.jpg"),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Image added", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=403, description="Unauthorized"),
     *      @OA\Response(response=404, description="Post does not exist")
     * )
     *
     * Add an image to a post.
     *
     * @param int $postID
     * @return JsonResponse
     */
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
        $image = Image::create([
            'image_url' => request()->input('image_url'),
            'user_id' => request()->user()->id,
            'post_id' => $postID,
        ]);

        return response()->json($image);
    }

    /**
     * @OA\Delete(
     *      path="feed/posts/{post}/images/{image}",
     *      summary="Delete an image from a post.",
     *      tags={"Images"},
     *      @OA\Response(response=200, description="Image deleted", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=403, description="Unauthorized"),
     *      @OA\Response(response=404, description="Image does not exist")
     * )
     *
     * Delete an image from a post.
     *
     * @param int $postID
     * @param int $imageID
     * @return JsonResponse
     */
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

    /**
     * @OA\Put(
     *      path="feed/posts/{post}/images/{image}",
     *      summary="Update an image for a post.",
     *      tags={"Images"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"image_url"},
     *              @OA\Property(property="image_url", type="string", format="url", example="https://example.com/image.jpg"),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Image updated", @OA\JsonContent()),
     *      @OA\Response(response=422, description="Invalid input", @OA\JsonContent()),
     *      @OA\Response(response=403, description="Unauthorized"),
     * )
     *
     * Update an image for a post.
     *
     * @param int $postID
     * @param int $imageID
     * @return JsonResponse
     */
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
