<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Feed\PostsController;
use App\Http\Controllers\Feed\LikesController;
use App\Http\Controllers\Feed\CommentsController;
use App\Http\Controllers\Feed\FriendsController;
use App\Http\Controllers\Feed\ImagesController;
use App\Http\Controllers\Feed\VideosController;
use App\Http\Controllers\Feed\RepostsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

    // Feed routes

    // Posts
    Route::get('feed/posts', [PostsController::class, 'getPosts']);
    Route::post('feed/posts', [PostsController::class, 'createPost']);
    Route::delete('feed/posts/{post}', [PostsController::class, 'deletePost']);
    Route::put('feed/posts/{post}', [PostsController::class, 'updatePost']);

    // Reposts
    Route::get('feed/posts/reposts', [RepostsController::class, 'getUserReposts']);
    Route::get('feed/posts/{post}/reposts', [RepostsController::class, 'getPostReposts']);
    Route::post('feed/posts/{post}/reposts', [RepostsController::class, 'repost']);
    Route::delete('feed/posts/{post}/reposts/{repost}', [RepostsController::class, 'unrepost']);

    // Comments
    Route::get('feed/posts/comments', [CommentsController::class, 'getUserComments']);
    Route::get('feed/posts/{post}/comments', [CommentsController::class, 'getPostComments']);
    Route::post('feed/posts/{post}/comments', [CommentsController::class, 'createComment']);
    Route::delete('feed/posts/{post}/comments/{comment}', [CommentsController::class, 'deleteComment']);
    Route::put('feed/posts/{post}/comments/{comment}', [CommentsController::class, 'updateComment']);

    // Likes
    Route::get('feed/posts/likes', [LikesController::class, 'getUserLikes']);
    Route::get('feed/posts/{post}/likes', [LikesController::class, 'getPostLikes']);
    Route::post('feed/posts/{post}/likes', [LikesController::class, 'likePost']);
    Route::delete('feed/posts/{post}/likes', [LikesController::class, 'unlikePost']);

    // Friends
    Route::get('feed/friends', [FriendsController::class, 'getUserFriends']);
    Route::get('feed/friends/requests', [FriendsController::class, 'getFriendRequests']);
    Route::get('feed/friends/blocked', [FriendsController::class, 'getBlockedFriends']);
    Route::get('feed/friends/declined', [FriendsController::class, 'getDeclinedFriends']);
    Route::get('feed/friends/pending', [FriendsController::class, 'getPendingFriends']);
    Route::get('feed/friends/{user}', [FriendsController::class, 'getUserFriendsById']);
    Route::post('feed/friends/{user}', [FriendsController::class, 'sendFriendRequest']);
    Route::delete('feed/friends/{user}', [FriendsController::class, 'removeFriend']);
    Route::put('feed/friends/{user}', [FriendsController::class, 'updateFriendship']);

    // Images
    Route::get('feed/images', [ImagesController::class, 'getUserImages']);
    Route::get('feed/posts/{post}/images', [ImagesController::class, 'getPostImages']);
    Route::post('feed/posts/{post}/images', [ImagesController::class, 'addImage']);
    Route::delete('feed/posts/{post}/images/{image}', [ImagesController::class, 'deleteImage']);
    Route::put('feed/posts/{post}/images/{image}', [ImagesController::class, 'updateImage']);

    // Videos
    Route::get('feed/videos', [VideosController::class, 'getUserVideos']);
    Route::get('feed/posts/{post}/videos', [VideosController::class, 'getPostVideos']);
    Route::post('feed/posts/{post}/videos', [VideosController::class, 'addVideo']);
    Route::delete('feed/posts/{post}/videos/{video}', [VideosController::class, 'deleteVideo']);
    Route::put('feed/posts/{post}/videos/{video}', [VideosController::class, 'updateVideo']);

    Route::get('feed/posts/{post}', [PostsController::class, 'getPost']);
});
