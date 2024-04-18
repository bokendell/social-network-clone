<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\FriendResource;
use App\Http\Controllers\Feed\PostsController;
use App\Http\Controllers\Feed\RepostsController;
use App\Http\Controllers\Feed\LikesController;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display the user's profile.
     */
    public function show($userID): Response
    {
        $user = User::findOrFail($userID);
        $followers = FriendResource::collection($user->followers());
        $following = FriendResource::collection($user->following());
        $postController = new PostsController();
        $posts = $postController->getUserPosts($userID)->getData();
        $repostController = new RepostsController();
        $reposts = $repostController->getUserReposts($userID)->getData();
        $likeController = new LikesController();
        $likes = $likeController->getUserLikes($userID)->getData();
        return Inertia::render('Profile/Show', [
            'user' => $user,
            'followers' => $followers,
            'following' => $following,
            'posts' => $posts,
            'reposts' => $reposts,
            'likes' => $likes,
        ]);
    }
}
