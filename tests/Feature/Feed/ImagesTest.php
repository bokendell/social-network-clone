<?php

use App\Models\Image;
use App\Models\Post;
use App\Models\User;

// ------------------------------ Get user images ------------------------------
test('get user images', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $images = Image::factory()->count(2)->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->get('feed/images');
    $response->assertStatus(200);
    $response->assertOk();
});

test('get user images with no images', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('feed/images');
    $response->assertStatus(200);
    $response->assertOk();
});

test('get user images only returns users images', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $userb->id]);
    $images = Image::factory()->count(2)->create(['user_id' => $userb->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->get('feed/images');
    $response->assertStatus(200);
    $response->assertOk();
    $response->assertJsonCount(0);
});

// ------------------------------ Get post images ------------------------------
test('get post images', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $images = Image::factory()->count(2)->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->get("feed/posts/{$post->id}/images");
    $response->assertStatus(200);
    $response->assertOk();
});

test('get post images with no images', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get("feed/posts/{$post->id}/images");
    $response->assertStatus(200);
    $response->assertOk();
});

test('get post images only returns post images', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $userb->id]);
    $images = Image::factory()->count(2)->create(['user_id' => $userb->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->get("feed/posts/{$post->id}/images");
    $response->assertStatus(200);
    $response->assertOk();
    $response->assertJsonCount(0);
});

test('get post images of non existent post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get("feed/posts/1/images");
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors('post_id');
});

test('get post images with string as post id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get("feed/posts/invalid/images");
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors('post_id');
});

// ------------------------------ Add images ------------------------------
test('add image', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $images = Image::factory()->count(2)->create(['user_id' => $user->id, 'post_id' => $post->id, 'image_url' => 'https://example.com/image.jpg']);
    $response = $this->actingAs($user)->post('feed/posts/'. $post->id . '/images', [
        'image_url' => 'https://example.com/image.jpg',
    ]);
    $response->assertStatus(200);
    $response->assertJson(['image_url' => 'https://example.com/image.jpg']);
    $response->assertOk();
});

test('add image to non existent post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('feed/posts/1/images', [
        'image_url' => 'https://example.com/image.jpg',
    ]);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors('post_id');
});

test('add image with invalid input', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post('feed/posts/'. $post->id . '/images', [
        'image_url' => 'invalid',
    ]);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors('image_url');
});

test('add image to post that does not belong to user', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $userb->id]);

    $response = $this->actingAs($user)->post('feed/posts/'. $post->id . '/images', [
        'image_url' => 'https://example.com/image.jpg',
    ]);
    $response->assertStatus(403);
    $response->assertForbidden();
});

test('add image to post with string as post id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $images = Image::factory()->count(2)->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->post('feed/posts/string/images', [
        'image_url' => 'https://example.com/image.jpg',
    ]);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors('post_id');
});

// ------------------------------ Delete images ------------------------------
test('delete image', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $image = Image::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->delete("feed/posts/{$post->id}/images/{$image->id}");
    $response->assertStatus(200);
    $response->assertOk();
});

test('delete non existent image', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete("feed/posts/{$post->id}/images/1");
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['image_id']);
});

test('delete image of post that does not belong to user', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $userb->id]);
    $image = Image::factory()->create(['user_id' => $userb->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->delete("feed/posts/{$post->id}/images/{$image->id}");
    $response->assertStatus(403);
    $response->assertForbidden();
});

test('delete image of post with string as post id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $image = Image::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->delete("feed/posts/string/images/{$image->id}");
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors('post_id');
});

test('delete image with string as image id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete("feed/posts/{$post->id}/images/string");
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors('image_id');
});

// ------------------------------ Update images ------------------------------
test('update image', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $image = Image::factory()->create(['user_id' => $user->id, 'post_id' => $post->id, 'image_url' => 'https://example.com/image.jpg']);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/images/{$image->id}", [
        'image_url' => 'https://example.com/image.jpg',
    ]);
    $response->assertStatus(200);
    $response->assertJson(['image_url' => 'https://example.com/image.jpg']);
    $response->assertOk();
});

test('update non existent image', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/images/1", [
        'image_url' => 'https://example.com/image.jpg',
    ]);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['image_id']);
});

test('update image with invalid input', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $image = Image::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/images/{$image->id}", [
        'image_url' => 'invalid',
    ]);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors('image_url');
});

test('update image with non existent post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $image = Image::factory()->create(['user_id' => $user->id, 'post_id' => $post->id, 'image_url' => 'https://example.com/image.jpg']);

    $response = $this->actingAs($user)->put("feed/posts/2/images/{$image->id}", [
        'image_url' => 'https://example.com/image.jpg',
    ]);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors('post_id');
});

test('update image of post that does not belong to user', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $userb->id]);
    $image = Image::factory()->create(['user_id' => $userb->id, 'post_id' => $post->id, 'image_url' => 'https://example.com/image.jpg']);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/images/{$image->id}", [
        'image_url' => 'https://example.com/image.jpg',
    ]);
    $response->assertStatus(403);
    $response->assertForbidden();
});

test('update image of post with string as post id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $image = Image::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->put("feed/posts/string/images/{$image->id}", [
        'image_url' => 'https://example.com/image.jpg',
    ]);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors('post_id');
});

test('update image with string as image id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/images/string", [
        'image_url' => 'https://example.com/image.jpg',
    ]);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors('image_id');
});

test('update image with string as image and post id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put("feed/posts/string/images/string", [
        'image_url' => 'https://example.com/image.jpg',
    ]);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id', 'image_id']);
});
