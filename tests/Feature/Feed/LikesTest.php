<?php
use App\Models\Post;
use App\Models\User;
use App\Models\Like;

// ------------------------------ Get user likes ------------------------------
test('get user likes', function () {
    $users = User::factory()->count(5)->create();
    $user = $users->first();
    $posts = [];

    foreach ($users as $user) {
        array_push($posts, Post::factory()->create([
            'user_id' => $user->id,
        ]));
    }
    foreach ($posts as $post) {
        Like::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    $response = $this->actingAs($user)->get('feed/posts/likes');

    $response->assertStatus(200);
    $response->assertJsonCount(5);
});

test('get user likes with no likes', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('feed/posts/likes');

    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get user likes with no posts', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('feed/posts/likes');

    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get user likes with no posts and no likes', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('feed/posts/likes');

    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

// ------------------------------ Get post likes ------------------------------
test('get post likes', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    Like::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->get("feed/posts/{$post->id}/likes");

    $response->assertStatus(200);
    $response->assertJsonCount(1);
});

test('get post likes with no likes', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->get("feed/posts/{$post->id}/likes");

    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('get post likes with no post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('feed/posts/1/likes');

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

test('get post likes with string as post id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('feed/posts/abc/likes');

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

// ------------------------------ like post ------------------------------
test('like post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post("feed/posts/{$post->id}/likes");

    $response->assertStatus(200);
    $this->assertDatabaseHas('likes', [
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);
});

test('like post with no post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('feed/posts/1/likes');

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

test('like post that is already liked', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    Like::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post("feed/posts/{$post->id}/likes");

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Post already liked']);
});

test('like post with string as post id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('feed/posts/abc/likes');

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

// ------------------------------ unlike post ------------------------------
test('unlike post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    Like::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->delete("feed/posts/{$post->id}/likes");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('likes', [
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);
});

test('unlike post with no post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete('feed/posts/1/likes');

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

test('unlike post with no like', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->delete("feed/posts/{$post->id}/likes");

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Like not found']);
});

test('unlike post that is not users', function () {
    $users = User::factory()->count(2)->create();
    $user = $users->first();
    $post = Post::factory()->create([
        'user_id' => $users->last()->id,
    ]);

    Like::factory()->create([
        'post_id' => $post->id,
        'user_id' => $users->last()->id,
    ]);

    $response = $this->actingAs($user)->delete("feed/posts/{$post->id}/likes");

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Like not found']);
});

test('unlike post with string as post id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete('feed/posts/abc/likes');

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});
