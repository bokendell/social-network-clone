<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Friend;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Like;
use App\Models\Repost;
use App\Models\Video;

use function Pest\Laravel\json;

// ------------------------------ Get user posts ------------------------------
test('get posts for user', function () {
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
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 1],
        'posts' => [
            [
                'id' => $post->id,
                'content' => $post->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $post->created_at->toJSON(),
                'updated_at' => $post->updated_at->toJSON(),
                'comments' => [],
                'likes' => [],
                'images' => [],
                'videos' => [],
                'reposts' => [],
            ]
        ],
    ]);
    $decodedResponse = $response->json();
    $this->assertCount(1, $decodedResponse['posts']);
});

test('get posts for user with multiple posts', function() {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $posts = Post::factory(2)->create([
        'user_id' => $friend->id,
    ]);
    Friend::create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'accepted',
    ]);

    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 2],
        'posts' => [
            [
                'id' => $posts[0]->id,
                'content' => $posts[0]->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $posts[0]->created_at->toJSON(),
                'updated_at' => $posts[0]->updated_at->toJSON(),
                'comments' => [],
                'likes' => [],
                'images' => [],
                'videos' => [],
                'reposts' => [],
            ],
            [
                'id' => $posts[1]->id,
                'content' => $posts[1]->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $posts[1]->created_at->toJSON(),
                'updated_at' => $posts[1]->updated_at->toJSON(),
                'comments' => [],
                'likes' => [],
                'images' => [],
                'videos' => [],
                'reposts' => [],
            ],
        ],
    ]);
    $decodedResponse = $response->json();
    $this->assertCount(2, $decodedResponse['posts']);
});

test('get posts for user with comments', function() {
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
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $friend->id,
    ]);

    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 1],
        'posts' => [
            [
                'id' => $post->id,
                'content' => $post->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $post->created_at->toJSON(),
                'updated_at' => $post->updated_at->toJSON(),
                'comments' => [
                    [
                        'id' => $comment->id,
                        'user' => [
                            'id' => $comment->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'post' => $comment->post_id,
                        'content' => $comment->content,
                        'created_at' => $comment->created_at->toJSON(),
                        'updated_at'=> $comment->updated_at->toJSON(),
                    ]
                ],
                'likes' => [],
                'images' => [],
                'videos' => [],
                'reposts' => [],
            ]
        ],
    ]);
    $decodedResponse = $response->json();
    $this->assertCount(1, $decodedResponse['posts']);
});

test('get posts for user with multiple comments', function() {
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
    $comments = Comment::factory(2)->create([
        'post_id' => $post->id,
        'user_id' => $friend->id,
    ]);

    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 1],
        'posts' => [
            [
                'id' => $post->id,
                'content' => $post->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $post->created_at->toJSON(),
                'updated_at' => $post->updated_at->toJSON(),
                'comments' => [
                    [
                        'id' => $comments[0]->id,
                        'user' => [
                            'id' => $comments[0]->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'post' => $comments[0]->post_id,
                        'content' => $comments[0]->content,
                        'created_at' => $comments[0]->created_at->toJSON(),
                        'updated_at'=> $comments[0]->updated_at->toJSON(),
                    ],
                    [
                        'id' => $comments[1]->id,
                        'user' => [
                            'id' => $comments[1]->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'post' => $comments[1]->post_id,
                        'content' => $comments[1]->content,
                        'created_at' => $comments[1]->created_at->toJSON(),
                        'updated_at'=> $comments[1]->updated_at->toJSON(),
                    ]
                ],
                'likes' => [],
                'images' => [],
                'videos' => [],
                'reposts' => [],
            ]
        ],
    ]);
    $decodedResponse = $response->json();
    $this->assertCount(1, $decodedResponse['posts']);
});

test('get posts for user with likes', function() {
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
    $like = Like::factory()->create([
        'post_id' => $post->id,
        'user_id' => $friend->id,
    ]);

    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 1],
        'posts' => [
            [
                'id' => $post->id,
                'content' => $post->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $post->created_at->toJSON(),
                'updated_at' => $post->updated_at->toJSON(),
                'comments' => [],
                'likes' => [
                    [
                        'id' => $like->id,
                        'user' => [
                            'id' => $like->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'post' => $like->post_id,
                        'created_at' => $like->created_at->toJSON(),
                        'updated_at'=> $like->updated_at->toJSON(),
                    ]
                ],
                'images' => [],
                'videos' => [],
                'reposts' => [],
            ]
        ],
    ]);
    $decodedResponse = $response->json();
    $this->assertCount(1, $decodedResponse['posts']);
});

test('get posts for user with multiple likes', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $friendb = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $friend->id,
    ]);
    Friend::create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'accepted',
    ]);
    $likes = Like::factory()->create([
        'post_id' => $post->id,
        'user_id' => $friend->id,
    ]);
    $likesb = Like::factory()->create([
        'post_id' => $post->id,
        'user_id' => $friendb->id,
    ]);

    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 1],
        'posts' => [
            [
                'id' => $post->id,
                'content' => $post->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $post->created_at->toJSON(),
                'updated_at' => $post->updated_at->toJSON(),
                'comments' => [],
                'likes' => [
                    [
                        'id' => $likes->id,
                        'user' => [
                            'id' => $likes->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'post' => $likes->post_id,
                        'created_at' => $likes->created_at->toJSON(),
                        'updated_at'=> $likes->updated_at->toJSON(),
                    ],
                    [
                        'id' => $likesb->id,
                        'user' => [
                            'id' => $likesb->user_id,
                            'name' => $friendb->name,
                            'username' => $friendb->username,
                        ],
                        'post' => $likesb->post_id,
                        'created_at' => $likesb->created_at->toJSON(),
                        'updated_at'=> $likesb->updated_at->toJSON(),
                    ],
                ],
                'images' => [],
                'videos' => [],
                'reposts' => [],
            ]
        ],
    ]);
    $decodedResponse = $response->json();
    $this->assertCount(1, $decodedResponse['posts']);
});

test('get posts for user with image', function () {
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
    $image = Image::factory()->create([
        'post_id' => $post->id,
        'user_id' => $friend->id,
    ]);

    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 1],
        'posts' => [
            [
                'id' => $post->id,
                'content' => $post->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $post->created_at->toJSON(),
                'updated_at' => $post->updated_at->toJSON(),
                'comments' => [],
                'likes' => [],
                'images' => [
                    [
                        'id' => $image->id,
                        'url' => $image->image_url,
                        'post' => $image->post_id,
                        'user' => [
                            'id' => $image->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'created_at' => $image->created_at->toJSON(),
                        'updated_at' => $image->updated_at->toJSON(),
                    ]
                ],
                'videos' => [],
                'reposts' => [],
            ]
        ],
    ]);
    $decodedResponse = $response->json();
    $this->assertCount(1, $decodedResponse['posts']);

});

test('get posts for user with multiple images', function () {
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
    $images = Image::factory(2)->create([
        'post_id' => $post->id,
        'user_id' => $friend->id,
    ]);

    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 1],
        'posts' => [
            [
                'id' => $post->id,
                'content' => $post->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $post->created_at->toJSON(),
                'updated_at' => $post->updated_at->toJSON(),
                'comments' => [],
                'likes' => [],
                'images' => [
                    [
                        'id' => $images[0]->id,
                        'url' => $images[0]->image_url,
                        'post' => $images[0]->post_id,
                        'user' => [
                            'id' => $images[0]->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'created_at' => $images[0]->created_at->toJSON(),
                        'updated_at' => $images[0]->updated_at->toJSON(),
                    ],
                    [
                        'id' => $images[1]->id,
                        'url' => $images[1]->image_url,
                        'post' => $images[1]->post_id,
                        'user' => [
                            'id' => $images[1]->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'created_at' => $images[1]->created_at->toJSON(),
                        'updated_at' => $images[1]->updated_at->toJSON(),
                    ]
                ],
                'videos' => [],
                'reposts' => [],
            ],
        ],
    ]);
    $decodedResponse = $response->json();
    $this->assertCount(1, $decodedResponse['posts']);
});

test('get posts for user with video', function () {
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
    $video = Video::factory()->create([
        'post_id' => $post->id,
        'user_id' => $friend->id,
    ]);

    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 1],
        'posts' => [
            [
                'id' => $post->id,
                'content' => $post->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $post->created_at->toJSON(),
                'updated_at' => $post->updated_at->toJSON(),
                'comments' => [],
                'likes' => [],
                'images' => [],
                'videos' => [
                    [
                        'id' => $video->id,
                        'url' => $video->video_url,
                        'post' => $video->post_id,
                        'user' => [
                            'id' => $video->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'created_at' => $video->created_at->toJSON(),
                        'updated_at' => $video->updated_at->toJSON(),
                    ]
                ],
                'reposts' => [],
            ]
        ],
    ]);
    $decodedResponse = $response->json();
    $this->assertCount(1, $decodedResponse['posts']);

});

test('get posts for user with multiple videos', function () {
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
    $videos = Video::factory(2)->create([
        'post_id' => $post->id,
        'user_id' => $friend->id,
    ]);

    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 1],
        'posts' => [
            [
                'id' => $post->id,
                'content' => $post->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $post->created_at->toJSON(),
                'updated_at' => $post->updated_at->toJSON(),
                'comments' => [],
                'likes' => [],
                'images' => [],
                'videos' => [
                    [
                        'id' => $videos[0]->id,
                        'url' => $videos[0]->video_url,
                        'post' => $videos[0]->post_id,
                        'user' => [
                            'id' => $videos[0]->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'created_at' => $videos[0]->created_at->toJSON(),
                        'updated_at' => $videos[0]->updated_at->toJSON(),
                    ],
                    [
                        'id' => $videos[1]->id,
                        'url' => $videos[1]->video_url,
                        'post' => $videos[1]->post_id,
                        'user' => [
                            'id' => $videos[1]->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'created_at' => $videos[1]->created_at->toJSON(),
                        'updated_at' => $videos[1]->updated_at->toJSON(),
                    ]
                ],
                'reposts' => [],
            ],
        ],
    ]);
});

test('get posts for user with repost', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $friendb = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $friend->id,
    ]);
    $repost = Repost::factory()->create([
        'user_id' => $friendb->id,
        'post_id' => $post->id,
    ]);
    Friend::create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'accepted',
    ]);
    Friend::create([
        'requester_id' => $user->id,
        'accepter_id' => $friendb->id,
        'status' => 'accepted',
    ]);

    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 1],
        'posts' => [
            [
                'id' => $post->id,
                'content' => $post->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $post->created_at->toJSON(),
                'updated_at' => $post->updated_at->toJSON(),
                'comments' => [],
                'likes' => [],
                'images' => [],
                'videos' => [],
                'reposts' => [
                    [
                        'id' => $repost->id,
                        'user' => [
                            'id' => $friendb->id,
                            'name' => $friendb->name,
                            'username' => $friendb->username,
                        ],
                        'post' => $post->id,
                        'created_at' => $repost->created_at->toJSON(),
                        'updated_at' => $repost->updated_at->toJSON(),
                    ],
                ],
            ],
        ],
    ]);
    $decodedResponse = $response->json();
    $this->assertCount(1, $decodedResponse['posts']);
});

test('get posts for user with multiple reposts', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $friendb = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $friend->id,
    ]);
    $repostA = Repost::factory()->create([
        'user_id' => $friendb->id,
        'post_id' => $post->id,
    ]);
    $repostB = Repost::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
    Friend::create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'accepted',
    ]);
    Friend::create([
        'requester_id' => $user->id,
        'accepter_id' => $friendb->id,
        'status' => 'accepted',
    ]);

    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 1],
        'posts' => [
            [
                'id' => $post->id,
                'content' => $post->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $post->created_at->toJSON(),
                'updated_at' => $post->updated_at->toJSON(),
                'comments' => [],
                'likes' => [],
                'images' => [],
                'videos' => [],
                'reposts' => [
                    [
                        'id' => $repostB->id,
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'username' => $user->username,
                        ],
                        'post' => $post->id,
                        'created_at' => $repostA->created_at->toJSON(),
                        'updated_at' => $repostA->updated_at->toJSON(),
                    ],
                    [
                        'id' => $repostA->id,
                        'user' => [
                            'id' => $friendb->id,
                            'name' => $friendb->name,
                            'username' => $friendb->username,
                        ],
                        'post' => $post->id,
                        'created_at' => $repostB->created_at->toJSON(),
                        'updated_at' => $repostB->updated_at->toJSON(),
                    ],
                ],
            ],
        ],
    ]);
    $decodedResponse = $response->json();
    $this->assertCount(1, $decodedResponse['posts']);
});

test('get posts for user with one of everything', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $friendb = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $friend->id,
    ]);
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $friend->id,
    ]);
    $like = Like::factory()->create([
        'post_id' => $post->id,
        'user_id' => $friend->id,
    ]);
    $image = Image::factory()->create([
        'post_id' => $post->id,
        'user_id' => $friend->id,
    ]);
    $video = Video::factory()->create([
        'post_id' => $post->id,
        'user_id' => $friend->id,
    ]);
    $repost = Repost::factory()->create([
        'user_id' => $friendb->id,
        'post_id' => $post->id,
    ]);
    Friend::create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'accepted',
    ]);
    Friend::create([
        'requester_id' => $user->id,
        'accepter_id' => $friendb->id,
        'status' => 'accepted',
    ]);

    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 1],
        'posts' => [
            [
                'id' => $post->id,
                'content' => $post->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $post->created_at->toJSON(),
                'updated_at' => $post->updated_at->toJSON(),
                'comments' => [
                    [
                        'id' => $comment->id,
                        'user' => [
                            'id' => $comment->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'post' => $comment->post_id,
                        'content' => $comment->content,
                        'created_at' => $comment->created_at->toJSON(),
                        'updated_at'=> $comment->updated_at->toJSON(),
                    ]
                ],
                'likes' => [
                    [
                        'id' => $like->id,
                        'user' => [
                            'id' => $like->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'post' => $like->post_id,
                        'created_at' => $like->created_at->toJSON(),
                        'updated_at'=> $like->updated_at->toJSON(),
                    ]
                ],
                'images' => [
                    [
                        'id' => $image->id,
                        'url' => $image->image_url,
                        'post' => $image->post_id,
                        'user' => [
                            'id' => $image->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'created_at' => $image->created_at->toJSON(),
                        'updated_at' => $image->updated_at->toJSON(),
                    ]
                ],
                'videos' => [
                    [
                        'id' => $video->id,
                        'url' => $video->video_url,
                        'post' => $video->post_id,
                        'user' => [
                            'id' => $video->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'created_at' => $video->created_at->toJSON(),
                        'updated_at' => $video->updated_at->toJSON(),
                    ]
                ],
                'reposts' => [
                    [
                        'id' => $repost->id,
                        'user' => [
                            'id' => $friendb->id,
                            'name' => $friendb->name,
                            'username' => $friendb->username,
                        ],
                        'post' => $post->id,
                        'created_at' => $repost->created_at->toJSON(),
                        'updated_at' => $repost->updated_at->toJSON(),
                    ],
                ],
            ],
        ],
    ]);
    $decodedResponse = $response->json();
    $this->assertCount(1, $decodedResponse['posts']);
});

test('get posts for user with 2 post with multiple of each', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $friendb = User::factory()->create();
    $postA = Post::factory()->create([
        'user_id' => $friend->id,
    ]);
    $commentA = Comment::factory()->create([
        'post_id' => $postA->id,
        'user_id' => $friend->id,
    ]);
    $likeA = Like::factory()->create([
        'post_id' => $postA->id,
        'user_id' => $friend->id,
    ]);
    $imageA = Image::factory()->create([
        'post_id' => $postA->id,
        'user_id' => $friend->id,
    ]);
    $videoA = Video::factory()->create([
        'post_id' => $postA->id,
        'user_id' => $friend->id,
    ]);
    $repostA = Repost::factory()->create([
        'user_id' => $friendb->id,
        'post_id' => $postA->id,
    ]);
    $postB = Post::factory()->create([
        'user_id' => $friend->id,
    ]);
    $commentB = Comment::factory()->create([
        'post_id' => $postB->id,
        'user_id' => $friend->id,
    ]);
    $likeB = Like::factory()->create([
        'post_id' => $postB->id,
        'user_id' => $friend->id,
    ]);
    $imageB = Image::factory()->create([
        'post_id' => $postB->id,
        'user_id' => $friend->id,
    ]);
    $videoB = Video::factory()->create([
        'post_id' => $postB->id,
        'user_id' => $friend->id,
    ]);
    $repostB = Repost::factory()->create([
        'user_id' => $friendb->id,
        'post_id' => $postB->id,
    ]);
    Friend::create([
        'requester_id' => $user->id,
        'accepter_id' => $friend->id,
        'status' => 'accepted',
    ]);
    Friend::create([
        'requester_id' => $user->id,
        'accepter_id' => $friendb->id,
        'status' => 'accepted',
    ]);
    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200)
             ->assertJsonStructure([
                'meta' => ['total'],
                'posts' => [
                    '*' => [
                        'id', 'content', 'user', 'created_at', 'updated_at',
                        'comments', 'likes', 'images', 'videos', 'reposts'
                    ]
                ]
             ]);
    $response->assertJson([
        'meta' => ['total' => 2],
        'posts' => [
            [
                'id' => $postA->id,
                'content' => $postA->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $postA->created_at->toJSON(),
                'updated_at' => $postA->updated_at->toJSON(),
                'comments' => [
                    [
                        'id' => $commentA->id,
                        'user' => [
                            'id' => $commentA->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'post' => $commentA->post_id,
                        'content' => $commentA->content,
                        'created_at' => $commentA->created_at->toJSON(),
                        'updated_at'=> $commentA->updated_at->toJSON(),
                    ]
                ],
                'likes' => [
                    [
                        'id' => $likeA->id,
                        'user' => [
                            'id' => $likeA->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'post' => $likeA->post_id,
                        'created_at' => $likeA->created_at->toJSON(),
                        'updated_at'=> $likeA->updated_at->toJSON(),
                    ]
                ],
                'images' => [
                    [
                        'id' => $imageA->id,
                        'url' => $imageA->image_url,
                        'post' => $imageA->post_id,
                        'user' => [
                            'id' => $imageA->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'created_at' => $imageA->created_at->toJSON(),
                        'updated_at' => $imageA->updated_at->toJSON(),
                    ]
                ],
                'videos' => [
                    [
                        'id' => $videoA->id,
                        'url' => $videoA->video_url,
                        'post' => $videoA->post_id,
                        'user' => [
                            'id' => $videoA->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'created_at' => $videoA->created_at->toJSON(),
                        'updated_at' => $videoA->updated_at->toJSON(),
                    ]
                ],
                'reposts' => [
                    [
                        'id' => $repostA->id,
                        'user' => [
                            'id' => $friendb->id,
                            'name' => $friendb->name,
                            'username' => $friendb->username,
                        ],
                        'post' => $postA->id,
                        'created_at' => $repostA->created_at->toJSON(),
                        'updated_at' => $repostA->updated_at->toJSON(),
                    ],
                ],
            ],
            [
                'id' => $postB->id,
                'content' => $postB->content,
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                ],
                'created_at' => $postB->created_at->toJSON(),
                'updated_at' => $postB->updated_at->toJSON(),
                'comments' => [
                    [
                        'id' => $commentB->id,
                        'user' => [
                            'id' => $commentB->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'post' => $commentB->post_id,
                        'content' => $commentB->content,
                        'created_at' => $commentB->created_at->toJSON(),
                        'updated_at'=> $commentB->updated_at->toJSON(),
                    ]
                ],
                'likes' => [
                    [
                        'id' => $likeB->id,
                        'user' => [
                            'id' => $likeB->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'post' => $likeB->post_id,
                        'created_at' => $likeB->created_at->toJSON(),
                        'updated_at'=> $likeB->updated_at->toJSON(),
                    ]
                ],
                'images' => [
                    [
                        'id' => $imageB->id,
                        'url' => $imageB->image_url,
                        'post' => $imageB->post_id,
                        'user' => [
                            'id' => $imageB->user_id,
                            'name' => $friend->name,
                            'username' => $friend->username,
                        ],
                        'created_at' => $imageB->created_at->toJSON(),
                        'updated_at' => $imageB->updated_at->toJSON(),
                    ]
                ],
                'videos' => [
                    [
                        'id' => $videoB->id,
                        'url' => $videoB->video_url,
                        'post' => $videoB->post_id,
                        'user' => [
                            'id' => $videoB->user_id,
                            'name'=> $friend->name,
                            'username' => $friend->username,
                        ],
                        'created_at' => $videoB->created_at->toJSON(),
                        'updated_at' => $videoB->updated_at->toJSON(),
                    ]
                ],
                'reposts' => [
                    [
                        'id' => $repostB->id,
                        'user' => [
                            'id' => $friendb->id,
                            'name' => $friendb->name,
                            'username' => $friendb->username,
                        ],
                        'post' => $postB->id,
                        'created_at' => $repostB->created_at->toJSON(),
                        'updated_at' => $repostB->updated_at->toJSON(),
                    ],
                ],
            ],
        ],
    ]);
    $decodedResponse = $response->json();
    $this->assertCount(2, $decodedResponse['posts']);
});

test('get posts for user with no friends', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/feed/posts');
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'meta' => ['total'],
        'posts' => []
    ]);
    $response->assertJson([
        'meta' => ['total' => 0],
        'posts' => []
    ]);
});

test('get posts that are only posts of the user\'s friends', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $friend->id,
    ]);
    $response = $this->actingAs($user)->get('/feed/posts');
    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'meta' => ['total'],
            'posts' => []
        ]);
    $response->assertJson([
        'meta' => ['total' => 0],
        'posts' => []
    ]);
});


// ------------------------------ Get post ------------------------------
test('get a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);
    $response = $this->actingAs($user)->get('/feed/posts/' . $post->id);
    $response->assertStatus(200);
    $response->assertOk();
});

test('get a post that does not exist', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/feed/posts/1');
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

test('get a post with a string as post id', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/feed/posts/abc');
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

// ------------------------------ Create post ------------------------------
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
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['content']);
});


// ------------------------------ Delete post------------------------------
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
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});

test('delete a post with a string as post id', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->delete('/feed/posts/abc');
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
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

// ------------------------------ Update post ------------------------------
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
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
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
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['content']);
});

test('update a post with a string as post id', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->put('/feed/posts/abc', [
        'content' => 'This is an updated post',
    ]);
    $response->assertStatus(422);
    $response->assertJson(['message' => 'Invalid input']);
    $response->assertJsonValidationErrors(['post_id']);
});
