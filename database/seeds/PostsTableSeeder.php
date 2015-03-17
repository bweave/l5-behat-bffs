<?php

use App\Post;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PostsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('posts')->truncate();

        $faker = Faker::create();

        foreach(range(1,10) as $i)
        {
            Post::create([
                'title' => "Post {$i}",
                'body' => $faker->paragraph,
            ]);
        }
    }

}