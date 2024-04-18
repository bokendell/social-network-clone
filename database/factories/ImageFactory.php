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
            "https://i.pinimg.com/736x/47/75/79/4775793d1cb73fa9d7865c3548e8f3e8.jpg",
            "https://images.axios.com/xJe9n3SSqsDzCReCk8KIvv0V8RE=/854x0:4598x3744/1920x1920/2023/08/06/1691340829056.jpg",
            "https://yt3.googleusercontent.com/ytc/AIdro_kn8X2OzjVnxMBEci1GXWfTcmqQfGX9uHiD5DgyXAsJ9A=s900-c-k-c0x00ffffff-no-rj",
            "https://pbs.twimg.com/profile_images/1255113654049128448/J5Yt92WW_400x400.png",
            "https://avatars.githubusercontent.com/u/47703742?s=280&v=4",
            "https://vitejs.dev/logo-with-shadow.png",
            "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRstFIb9c2xX_tz60TZ7bIMiCSYJiKIEgQLnDv9OXYFlw&s",
        ];
        return Arr::random($freeImageUrls);
    }
}
