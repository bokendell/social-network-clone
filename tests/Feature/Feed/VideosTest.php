<?php

use App\Models\Video;
use App\Models\Post;
use App\Models\User;

// ------------------------------ Get User videos ------------------------------
test('get user videos', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $images = Video::factory()->count(2)->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->get('feed/videos');
    $response->assertStatus(200);
    $response->assertOk();
});

test('get user videos with no videos', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('feed/videos');
    $response->assertStatus(200);
    $response->assertOk();
});

test('get user videos only returns users videos', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $userb->id]);
    $images = Video::factory()->count(2)->create(['user_id' => $userb->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->get('feed/videos');
    $response->assertStatus(200);
    $response->assertOk();
    $response->assertJsonCount(0);
});

// ------------------------------ Get post videos ------------------------------
test('get post videos', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $images = Video::factory()->count(2)->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->get("feed/posts/{$post->id}/videos");
    $response->assertStatus(200);
    $response->assertOk();
});

test('get post videos with no videos', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get("feed/posts/{$post->id}/videos");
    $response->assertStatus(200);
    $response->assertOk();
});

test('get post videos only returns post videos', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $userb->id]);
    $images = Video::factory()->count(2)->create(['user_id' => $userb->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->get("feed/posts/{$post->id}/videos");
    $response->assertStatus(200);
    $response->assertOk();
    $response->assertJsonCount(0);
});

test('get post videos with no post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get("feed/posts/1/videos");
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

test('get post videos with string as post id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get("feed/posts/invalid/videos");
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

// ------------------------------ Add videos ------------------------------
test('add video', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post("feed/posts/{$post->id}/videos", [
        'video_url' => 'https://www.youtube.com/watch?v=12345'
    ]);

    $response->assertStatus(200);
    $response->assertOk();
    $response->assertJson(['video_url' => 'https://www.youtube.com/watch?v=12345']);
});

test('add video with invalid input', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post("feed/posts/{$post->id}/videos", [
        'video_url' => 'invalid'
    ]);

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
});

test('add video to post that does not belong to user', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $userb->id]);

    $response = $this->actingAs($user)->post('feed/posts/'. $post->id . '/videos', [
        'video_url' => 'https://example.com/image.jpg',
    ]);
    $response->assertStatus(403);
    $response->assertForbidden();
});

test('add video to non existent post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('feed/posts/1/videos', [
        'video_url' => 'https://www.youtube.com/watch?v=12345'
    ]);

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

test('add video with string as post id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('feed/posts/invalid/videos', [
        'video_url' => 'https://www.youtube.com/watch?v=12345'
    ]);

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

// ------------------------------ Delete videos ------------------------------
test('delete video', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $video = Video::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->delete("feed/posts/{$post->id}/videos/{$video->id}");
    $response->assertStatus(200);
    $response->assertOk();
    $response->assertJson(['message' => 'Video deleted']);
});

test('delete non existent video', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete("feed/posts/1/videos/1");
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['video_id']);
});

test('delete video with string as video id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete("feed/posts/{$post->id}/videos/invalid");
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['video_id']);
});

test('delete video with string as post id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete("feed/posts/invalid/videos/1");
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

test('delete other users video', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $userb->id]);
    $video = Video::factory()->create(['user_id' => $userb->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->delete("feed/posts/{$post->id}/videos/{$video->id}");
    $response->assertStatus(403);
    $response->assertJson(['message' => 'Unauthorized']);
});

// ------------------------------ Update videos ------------------------------
test('update video', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $video = Video::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/videos/{$video->id}", [
        'video_url' => 'https://www.youtube.com/watch?v=54321',

    ]);

    $response->assertStatus(200);
    $response->assertOk();
    $response->assertJson(['video_url' => 'https://www.youtube.com/watch?v=54321']);
});

test('update video with string as video id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/videos/invalid", [
        'video_url' => 'https://www.youtube.com/watch?v=54321',
    ]);

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['video_id']);
});

test('update video with string as post id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put("feed/posts/invalid/videos/1", [
        'video_url' => 'https://www.youtube.com/watch?v=54321',
    ]);

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

test('update video with string as post and video id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put("feed/posts/invalid/videos/invalid", [
        'video_url' => 'https://www.youtube.com/watch?v=54321',
    ]);

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id', 'video_id']);
});

test('update video with invalid input', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $video = Video::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/videos/{$video->id}", [
        'video_url' => 'invalid',
    ]);

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['video_url']);
});

test('update video with no video url', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $video = Video::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/videos/{$video->id}", [
    ]);

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['video_url']);
});

test('update non existent video', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put("feed/posts/1/videos/1", [
        'video_url' => 'https://www.youtube.com/watch?v=54321',
    ]);

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['video_id']);
});

test('update video with non existent post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $video = Video::factory()->create(['user_id' => $user->id, 'post_id' => $post->id, 'video_url' => 'https://www.youtube.com/watch?v=54321']);

    $response = $this->actingAs($user)->put("feed/posts/2/videos/{$video->id}", [
        'video_url' => 'https://www.youtube.com/watch?v=54321'
    ]);

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

test('update other users video', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $userb->id]);
    $video = Video::factory()->create(['user_id' => $userb->id, 'post_id' => $post->id, 'video_url' => 'https://www.youtube.com/watch?v=54321']);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/videos/{$video->id}", [
        'video_url' => 'https://www.youtube.com/watch?v=54321'
    ]);

    $response->assertStatus(403);
    $response->assertJson(['message' => 'Unauthorized']);
});
