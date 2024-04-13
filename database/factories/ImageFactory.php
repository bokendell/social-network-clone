<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'user_id' => User::factory(),
            // 'post_id' => Post::factory(),
            'image_url' => $this->getImageURL(),
        ];
    }

    private function getImageURL(): string
    {
        $freeImageUrls = [
            "https://images.unsplash.com/photo-1584697964156-9dec3f64631e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80",
            "https://images.unsplash.com/photo-1576158113924-477cfced1075?ixlib=rb-1.2.1&auto=format&fit=crop&w=1352&q=80",
            "https://images.pexels.com/photos/207962/pexels-photo-207962.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260",
            "https://images.pexels.com/photos/417074/pexels-photo-417074.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260",
            "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885_960_720.jpg",
            "https://cdn.pixabay.com/photo/2015/06/19/21/24/avenue-815297_960_720.jpg",
            "https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80",
            "https://images.unsplash.com/photo-1531256379419-9b831d9236f8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80",
            "https://images.pexels.com/photos/326055/pexels-photo-326055.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260",
            "https://cdn.pixabay.com/photo/2013/10/02/23/03/mountains-190055_960_720.jpg"
        ];
        return Arr::random($freeImageUrls);
    }
}
