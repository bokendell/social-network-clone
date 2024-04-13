<?php

use Illuminate\Support\Facades\Route;

Route::get("/feed/posts", "Feed\PostsController@getPosts");
Route::post("/feed/posts", "Feed\PostsController@createPost");
Route::delete("/feed/posts/{post}", "Feed\PostsController@deletePost");
Route::put("/feed/posts/{post}", "Feed\PostsController@updatePost");
