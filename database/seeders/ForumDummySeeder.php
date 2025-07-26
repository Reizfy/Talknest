<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Nest;
use App\Models\NestUser;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Vote;

class ForumDummySeeder extends Seeder
{
    public function run()
    {
        // Create users
        $users = User::all();

        $nest = Nest::create([
            'name' => 'limbuscompany',
            'description' => 'Face the Sin, Save the EGO. Unofficial fan subreddit for the mobile/Steam game Limbus Company. As the Executive Manager, lead your group of twelve Sinners, venture into the buried facilities of Lobotomy Corporation, and lay claim on the Golden Boughs.',
            'owner_id' => $users[0]->id,
            'profile_image' => 'limbuscompany.png',
            'banner' => 'limbuscompany.png',
        ]);

        foreach ($users as $i => $user) {
            if ($i < 10) {
                NestUser::create([
                    'nest_id' => $nest->id,
                    'user_id' => $user->id,
                    'role' => $i == 0 ? 'moderator' : 'member',
                ]);
            }
        }

        // Create 20 posts for the nest
        for ($j = 0; $j < 20; $j++) {
            $post = Post::create([
                'user_id' => $users[$j % 10]->id,
                'nest_id' => $nest->id,
                'title' => "Post $j in limbus_company",
                'content' => "Ini adalah konten post $j di limbus_company",
            ]);
            // Add comments
            for ($k = 0; $k < 2; $k++) {
                $comment = Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $users[$k]->id,
                    'content' => "Comment $k on post $j in limbus_company",
                ]);
                // Add votes to comment
                foreach ($users as $voter) {
                    Vote::create([
                        'user_id' => $voter->id,
                        'votable_id' => $comment->id,
                        'votable_type' => Comment::class,
                        'value' => rand(-1,1),
                    ]);
                }
            }
            // Add votes to post
            foreach ($users as $voter) {
                Vote::create([
                    'user_id' => $voter->id,
                    'votable_id' => $post->id,
                    'votable_type' => Post::class,
                    'value' => rand(-1,1),
                ]);
            }
        }
    }
}
