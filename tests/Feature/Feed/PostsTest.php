<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Friend;

test('get posts for user', function () {
    $response = $this->get('/feed/posts');
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $friend->id,
    ]);
    Friend::create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'accepted',
    ]);

    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200);
    $response->assertOk();
});

test('get posts for user with no friends', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200);
    $response->assertOk();
});

test('create a post', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post('/feed/posts', [
        'content' => 'This is a post',
    ]);
    $response->assertStatus(200);
    $response->assertOk();
    $this->assertDatabaseHas('posts', [
        'user_id' => $user->id,
        'content' => 'This is a post',
    ]);
});

test('create a post with no content', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post('/feed/posts', []);
    $response->assertStatus(400);
    $response->assertJson([
        'message' => 'Content cannot be empty',
    ]);
});

test('delete a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);
    $response = $this->actingAs($user)->delete('/feed/posts/' . $post->id);
    $response->assertStatus(200);
    $response->assertOk();
    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
});

test('delete a post that does not exist', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->delete('/feed/posts/1');
    $response->assertStatus(400);
    $response->assertJson([
        'message' => 'Post does not exist',
    ]);
});

test('delete a post that does not belong to user', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $userB->id,
    ]);
    $response = $this->actingAs($userA)->delete('/feed/posts/' . $post->id);
    $response->assertStatus(403);
});

test('update a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);
    $response = $this->actingAs($user)->put('/feed/posts/' . $post->id, [
        'content' => 'This is an updated post',
    ]);
    $response->assertStatus(200);
    $response->assertOk();
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'content' => 'This is an updated post',
    ]);
});

test('update a post that does not exist', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->put('/feed/posts/1', [
        'content' => 'This is an updated post',
    ]);
    $response->assertStatus(400);
    $response->assertJson([
        'message' => 'Post does not exist',
    ]);
});

test('update a post that does not belong to user', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $userB->id,
    ]);
    $response = $this->actingAs($userA)->put('/feed/posts/' . $post->id, [
        'content' => 'This is an updated post',
    ]);
    $response->assertStatus(403);
});

test('update a post with no content', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);
    $response = $this->actingAs($user)->put('/feed/posts/' . $post->id, []);
    $response->assertStatus(400);
    $response->assertJson([
        'message' => 'Content cannot be empty',
    ]);
});

