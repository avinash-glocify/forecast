<?php

use Illuminate\Database\Seeder;
use App\Post;

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // DB::table('posts')->truncate();
       $posts = [
         [
            'user_id' => 1,
            'title'   => 'sunt aut facere repellat provident occaecati excepturi optio reprehenderit',
            'content' => 'quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architect',
         ],
         [
            'user_id' => 1,
            'title'   => 'qui est esse',
            'content' => 'est rerum tempore vitae\nsequi sint nihil reprehenderit dolor beatae ea dolores neque\nfugiat blanditiis voluptate porro vel nihil molestiae ut reiciendis\nqui aperiam non debitis possimus qui neque nisi nulla',
         ],
         [
            'user_id' => 1,
            'title'   => 'ea molestias quasi exercitationem repellat qui ipsa sit aut',
            'content' => 'et iusto sed quo iure\nvoluptatem occaecati omnis eligendi aut ad\nvoluptatem doloribus vel accusantium quis pariatur\nmolestiae porro eius odio et labore et velit aut',
         ],
         [
            'user_id' => 1,
            'title'   => 'eum et est occaecati',
            'content' => 'ullam et saepe reiciendis voluptatem adipisci\nsit amet autem assumenda provident rerum culpa\nquis hic commodi nesciunt rem tenetur doloremque ipsam iure\nquis sunt voluptatem rerum illo velit',
         ],
       ];

       foreach ($posts as $key => $post) {
         Post::updateOrCreate(['title' => $post['title']], ['user_id' => $post['user_id'], 'content' => $post['content']]);
       }
    }
}
