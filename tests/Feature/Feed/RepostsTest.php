<?php
use App\Models\Repost;
use App\Models\User;
use App\Models\Post;

// Test User Reposts
test('get user reposts', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $posts = Post::factory()->count(2)->create(['user_id' => $userb->id]);
    $reposts = [];
    foreach ($posts as $post) {
        array_push($reposts, Repost::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]));
    }

    $response = $this->actingAs($user)->get('feed/posts/reposts');
    $response->assertStatus(200);
    $response->assertOk();
});

test('get user reposts with no reposts', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('feed/posts/reposts');
    $response->assertStatus(200);
    $response->assertOk();
});

test('get user reposts only return user resposts', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $posts = Post::factory()->count(2)->create(['user_id' => $userb->id]);
    $reposts = [];
    foreach ($posts as $post) {
        array_push($reposts, Repost::factory()->create(['user_id' => $userb->id, 'post_id' => $post->id]));
    }

    $response = $this->actingAs($user)->get('feed/posts/reposts');
    $response->assertStatus(200);
    $response->assertOk();
    $response->assertJsonCount(0);
});

// Test Post Reposts
test('get post reposts', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $posts = Post::factory()->count(2)->create(['user_id' => $userb->id]);
    $reposts = [];
    foreach ($posts as $post) {
        array_push($reposts, Repost::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]));
    }

    $response = $this->actingAs($user)->get('feed/posts/' . $post->id . '/reposts');
    $response->assertStatus(200);
    $response->assertOk();
});

test('get post reposts with no reposts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get('feed/posts/' . $post->id . '/reposts');
    $response->assertStatus(200);
    $response->assertOk();
});

test('get reposts from non-existent post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('feed/posts/1/reposts');
    $response->assertStatus(400);
    $response->assertJson([
        'message' => 'Post does not exist',
    ]);
});

test('get reposts from post only return post\'s reposts', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $userc = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $userb->id]);
    $reposts = [];
    array_push($reposts, Repost::factory()->create(['user_id' => $userb->id, 'post_id' => $post->id]));
    array_push($reposts, Repost::factory()->create(['user_id' => $userc->id, 'post_id' => $post->id]));

    $response = $this->actingAs($user)->get('feed/posts/' . $post->id . '/reposts');
    $response->assertStatus(200);
    $response->assertOk();
    $response->assertJsonCount(0);
});

// Test Repost
test('repost post', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(
        ['user_id' => $userb->id]
    );

    $response = $this->actingAs($user)->post('feed/posts/' . $post->id . '/repost');
    $response->assertStatus(200);
    $response->assertOk();
});

test('repost non-existent post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('feed/posts/1/repost');
    $response->assertStatus(400);
    $response->assertJson([
        'message' => 'Post does not exist',
    ]);
});

test('repost already reposted post', function () {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(
        ['user_id' => $userb->id]
    );
    Repost::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->post('feed/posts/' . $post->id . '/repost');
    $response->assertStatus(400);
    $response->assertJson([
        'message' => 'Post already reposted',
    ]);
});

test('repost post users own post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(
        ['user_id' => $user->id]
    );

    $response = $this->actingAs($user)->post('feed/posts/' . $post->id . '/repost');
    $response->assertStatus(200);
    $response->assertOk();
});

// Test Unrepost
test('unrepost post', function() {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(
        ['user_id' => $userb->id]
    );
    $repost = Repost::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->delete('feed/posts/' . $post->id . '/repost');
    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Post unreposted',
    ]);
});

test('unrepost non-existent repost', function() {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $post = Post::factory()->create(
        ['user_id' => $userb->id]
    );

    $response = $this->actingAs($user)->delete('feed/posts/' . $post->id . '/repost');
    $response->assertStatus(400);
    $response->assertJson([
        'message' => 'Post not reposted',
    ]);
});

test('unrepost post users own post', function() {
    $user = User::factory()->create();
    $post = Post::factory()->create(
        ['user_id' => $user->id]
    );
    $repost = Repost::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->delete('feed/posts/' . $post->id . '/repost');
    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Post unreposted',
    ]);
});

test('unrepost repost that does not belong to user', function() {
    $user = User::factory()->create();
    $userb = User::factory()->create();
    $userc = User::factory()->create();
    $post = Post::factory()->create(
        ['user_id' => $userb->id]
    );
    $repost = Repost::factory()->create(['user_id' => $userc->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)->delete('feed/posts/' . $post->id . '/repost');
    $response->assertStatus(400);
    $response->assertJson([
        'message' => 'Post not reposted',
    ]);
});
