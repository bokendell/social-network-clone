<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;

// ------------------------------ Get user comments ------------------------------
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

// ------------------------------ Get post comments ------------------------------
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

    $response = $this->actingAs($user)->get("feed/posts/1/comments");

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Invalid input',
    ]);
    $response->assertJsonValidationErrors(['post_id']);
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

test('get post comments with string as post id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get("feed/posts/invalid/comments");

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Invalid input',
    ]);
    $response->assertJsonValidationErrors(['post_id']);
});

// ------------------------------ Create comment ------------------------------
test('create comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post("feed/posts/{$post->id}/comments", [
        'content' => 'This is a comment',
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

    $response = $this->actingAs($user)->post("feed/posts/{$post->id}/comments");

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Invalid input',
    ]);
    $response->assertJsonValidationErrors(['content']);
});

test('create comment with no post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post("feed/posts/1/comments", [
        'content' => 'This is a comment',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Invalid input',
    ]);
    $response->assertJsonValidationErrors(['post_id']);
});

test('create comment with string as post id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post("feed/posts/invalid/comments", [
        'content' => 'This is a comment',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Invalid input',
    ]);
    $response->assertJsonValidationErrors(['post_id']);
});

// ------------------------------ Update comment ------------------------------
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

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Invalid input',
    ]);
    $response->assertJsonValidationErrors(['content']);
});

test('update comment with no comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/comments/1", [
        'content' => 'This is an updated comment',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Invalid input',
    ]);
    $response->assertJsonValidationErrors(['comment_id']);
});

test('update comment with no post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    $response = $this->actingAs($user)->put("feed/posts/100/comments/{$comment->id}", [
        'content' => 'This is an updated comment',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Invalid input',
    ]);
    $response->assertJsonValidationErrors(['post_id']);
});

test('update comment with no post and comment', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put("feed/posts/1/comments/1", [
        'content' => 'This is an updated comment',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Invalid input',
    ]);
    $response->assertJsonValidationErrors(['post_id', 'comment_id']);
});

test('update comment with string as post id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    $response = $this->actingAs($user)->put("feed/posts/invalid/comments/{$comment->id}", [
        'content' => 'This is an updated comment',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Invalid input',
    ]);
    $response->assertJsonValidationErrors(['post_id']);
});

test('update comment with string as comment id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->put("feed/posts/{$post->id}/comments/invalid", [
        'content' => 'This is an updated comment',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Invalid input',
    ]);
    $response->assertJsonValidationErrors(['comment_id']);
});

test('update comment with string as post and comment id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put("feed/posts/invalid/comments/invalid", [
        'content' => 'This is an updated comment',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Invalid input',
    ]);
    $response->assertJsonValidationErrors(['post_id', 'comment_id']);
});

// ------------------------------ Delete comment------------------------------
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

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['comment_id']);
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

    $response->assertStatus(403);
    $response->assertJsonFragment([
        'message' => 'Unauthorized',
    ]);
});

test('delete comment with string as comment id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
    $response = $this->actingAs($user)->delete("feed/posts/{$post->id}/comments/invalid");
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['comment_id']);
});

test('delete comment with string as post id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
    $response = $this->actingAs($user)->delete("feed/posts/invalid/comments/{$comment->id}");
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

test('delete comment with string as post and comment id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete("feed/posts/invalid/comments/invalid");
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id', 'comment_id']);
});
