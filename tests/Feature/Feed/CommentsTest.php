<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;

// Test User Comments
test('get user comments', function () {
    $users = User::factory()->count(5)->create();
    $user = $users->first();
    $posts = [];

    foreach ($users as $user) {
        array_push($posts, Post::factory()->create([
            'user_id' => $user->id,
        ]));
    }

    foreach ($posts as $post) {
        Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    $response = $this->actingAs($user)->get('feed/posts/comments');

    $response->assertStatus(200);
    $response->assertJsonCount(5);
});

test('get user comments with no comments', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('feed/posts/comments');

    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get user comments with no posts', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('feed/posts/comments');

    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get user comments with no posts and no comments', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('feed/posts/comments');

    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

// Test Post Comments
test('get post comments', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);
    $comments = [];

    foreach (range(1, 5) as $i) {
        array_push($comments, Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]));
    }

    $response = $this->actingAs($user)->get("feed/posts/{$post->id}/comments");

    $response->assertStatus(200);
    $response->assertJsonCount(5);
});

test('get post comments with no comments', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->get("feed/posts/{$post->id}/comments");

    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get post comments with no post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->get("feed/posts/{$post->id}/comments");

    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get post comments with no post and no comments', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->get("feed/posts/{$post->id}/comments");

    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

// Test Create Comment
test('create comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post("feed/posts/{$post->id}/comments", [
        'content' => 'This is a comment',
        'post_id' => $post->id,
    ]);

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'content' => 'This is a comment',
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

test('create comment with no content', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post("feed/posts/{$post->id}/comments", [
        'post_id' => $post->id,
    ]);

    $response->assertStatus(400);
    $response->assertJsonFragment([
        'message' => 'Content is required',
    ]);
});

test('create comment with no post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post("feed/posts/1/comments", [
        'content' => 'This is a comment',
    ]);

    $response->assertStatus(404);
    $response->assertJsonFragment([
        'message' => 'Post not found',
    ]);
});

// Test Update Comment
test('update comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/comments/{$comment->id}", [
        'content' => 'This is an updated comment',
    ]);

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'content' => 'This is an updated comment',
    ]);
});

test('update comment with no content', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/comments/{$comment->id}", [
        'content' => '',
    ]);

    $response->assertStatus(400);
    $response->assertJsonFragment([
        'message' => 'Content is required',
    ]);
});

test('update comment with no comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/comments/1", [
        'content' => 'This is an updated comment',
    ]);

    $response->assertStatus(404);
    $response->assertJsonFragment([
        'message' => 'Comment not found',
    ]);
});

// Test Delete Comment
test('delete comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    $response = $this->actingAs($user)->delete("feed/posts/{$post->id}/comments/{$comment->id}");

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'message' => 'Comment deleted',
    ]);
});

test('delete comment on post with no comments', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->delete("feed/posts/{$post->id}/comments/1");

    $response->assertStatus(404);
    $response->assertJsonFragment([
        'message' => 'Comment not found',
    ]);
});

test('delete comment that does not belong to user', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    $user2 = User::factory()->create();

    $response = $this->actingAs($user2)->delete("feed/posts/{$post->id}/comments/{$comment->id}");

    $response->assertStatus(401);
    $response->assertJsonFragment([
        'message' => 'Unauthorized',
    ]);
});
