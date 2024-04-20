<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Feed\PostsController;
use App\Http\Controllers\Feed\LikesController;
use App\Http\Controllers\Feed\CommentsController;
use App\Http\Controllers\Feed\FriendsController;
use App\Http\Controllers\Feed\ImagesController;
use App\Http\Controllers\Feed\VideosController;
use App\Http\Controllers\Feed\RepostsController;
use App\Models\User;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $postController = new PostsController();
    $posts = $postController->getPosts(request())->getData();
    return Inertia::render('Dashboard', ['posts' => $posts]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/post', function () {
    return Inertia::render('CreatePost');
})->middleware(['auth', 'verified'])->name('create.post');

Route::get('/notifications', function () {
    $user = User::findOrFail(auth()->id());
    $friendsController = new FriendsController();
    $requests = $friendsController->getFriendRequests(request())->getData();
    return Inertia::render('Notifications', [
        'user' => $user,
        'requests' => $requests,
        ]);
})->middleware(['auth', 'verified'])->name('notifications');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Posts
    Route::get('/feed/posts', [PostsController::class, 'getPosts'])->name('feed.posts');
    Route::post('/feed/posts', [PostsController::class, 'createPost'])->name('feed.posts.create');
    Route::get('/feed/posts/{user}', [PostsController::class, 'getUserPosts'])->name('feed.posts.user');
    Route::delete('feed/posts/{post}', [PostsController::class, 'deletePost'])->name('feed.posts.delete');
    Route::put('feed/posts/{post}', [PostsController::class, 'updatePost'])->name('feed.posts.update');

    // Reposts
    Route::get('feed/posts/reposts', [RepostsController::class, 'getReposts'])->name('feed.posts.reposts');
    Route::get('/feed/posts/{user}/reposts', [RepostsController::class, 'getUserReposts'])->name('feed.posts.reposts.user');
    Route::get('feed/posts/{post}/reposts', [RepostsController::class, 'getPostReposts'])->name('feed.posts.reposts');
    Route::post('feed/posts/{post}/reposts', [RepostsController::class, 'repost'])->name('feed.posts.repost');
    Route::delete('feed/posts/{post}/reposts/{repost}', [RepostsController::class, 'unrepost'])->name('feed.posts.unrepost');

    // Comments
    Route::get('feed/posts/comments', [CommentsController::class, 'getUserComments'])->name('feed.posts.comments');
    Route::get('feed/posts/{post}/comments', [CommentsController::class, 'getPostComments'])->name('feed.posts.comments');
    Route::post('/feed/posts/{post}/comments', [CommentsController::class, 'createComment'])->name('feed.posts.comments.create');
    Route::delete('/feed/posts/{post}/comments/{comment}', [CommentsController::class, 'deleteComment'])->name('feed.posts.comments.delete');
    Route::put('feed/posts/{post}/comments/{comment}', [CommentsController::class, 'updateComment'])->name('feed.posts.comments.update');

    // Likes
    Route::get('feed/posts/likes', [LikesController::class, 'getLikes'])->name('feed.posts.likes');
    Route::get('feed/posts/{post}/likes', [LikesController::class, 'getPostLikes'])->name('feed.posts.likes');
    Route::post('/feed/posts/{post}/likes', [LikesController::class, 'likePost'])->name('feed.posts.likes.create');
    Route::delete('/feed/posts/{post}/likes', [LikesController::class, 'unlikePost'])->name('feed.posts.likes.delete');
    Route::get('/feed/posts/{user}/likes', [LikesController::class, 'getUserLikes'])->name('feed.posts.likes.user');

    // Friends
    Route::get('feed/friends', [FriendsController::class, 'getUserFriends'])->name('feed.friends');
    Route::get('feed/friends/requests', [FriendsController::class, 'getFriendRequests'])->name('feed.friends.requests');
    Route::get('feed/friends/blocked', [FriendsController::class, 'getBlockedFriends'])->name('feed.friends.blocked');
    Route::get('feed/friends/declined', [FriendsController::class, 'getDeclinedFriends'])->name('feed.friends.declined');
    Route::get('feed/friends/pending', [FriendsController::class, 'getPendingFriends'])->name('feed.friends.pending');
    Route::get('feed/friends/{user}', [FriendsController::class, 'getUserFriendsById'])->name('feed.friends.user');
    Route::post('feed/friends/{user}', [FriendsController::class, 'sendFriendRequest'])->name('feed.friends.create');
    Route::post('/feed/friends/follow/{user}', [FriendsController::class, 'followUser'])->name('feed.friends.follow');
    Route::delete('/feed/friends/{user}', [FriendsController::class, 'removeFriend'])->name('feed.friends.delete');
    Route::put('/feed/friends/{user}', [FriendsController::class, 'updateFriendship'])->name('feed.friends.update');

    // Images
    Route::get('feed/images', [ImagesController::class, 'getUserImages'])->name('feed.images');
    Route::get('feed/posts/{post}/images', [ImagesController::class, 'getPostImages'])->name('feed.images');
    Route::post('feed/posts/{post}/images', [ImagesController::class, 'addImage'])->name('feed.images.create');
    Route::delete('feed/posts/{post}/images/{image}', [ImagesController::class, 'deleteImage'])->name('feed.images.delete');
    Route::put('feed/posts/{post}/images/{image}', [ImagesController::class, 'updateImage'])->name('feed.images.update');

    // Videos
    Route::get('feed/videos', [VideosController::class, 'getUserVideos'])->name('feed.videos');
    Route::get('feed/posts/{post}/videos', [VideosController::class, 'getPostVideos'])->name('feed.videos');
    Route::post('feed/posts/{post}/videos', [VideosController::class, 'addVideo'])->name('feed.videos.create');
    Route::delete('feed/posts/{post}/videos/{video}', [VideosController::class, 'deleteVideo'])->name('feed.videos.delete');
    Route::put('feed/posts/{post}/videos/{video}', [VideosController::class, 'updateVideo'])->name('feed.videos.update');

    Route::get('feed/posts/{post}', [PostsController::class, 'getPost'])->name('feed.posts.get');
});



require __DIR__.'/auth.php';
