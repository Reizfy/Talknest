<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        // 1 admin
        $admin = User::create([
            'username' => 'NestsAdmin',
            'display_name' => null,
            'email' => 'admin@mail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'user_type' => 'admin',
            'gender' => $faker->randomElement(['male', 'female']),
            'status' => 'active',
            'avatar' => 'avatars/' . $faker->word . '.png',
            'banner' => 'banners/' . $faker->word . '.png',
            'bio' => $faker->sentence,
            'remember_token' => null,
        ]);
        $admin->assignRole('admin');

        // 19 users
        for ($i = 0; $i < 19; $i++) {
            $rawUsername = preg_replace('/[^a-zA-Z0-9]/', '', $faker->unique()->userName);
            $username = strtolower($rawUsername . rand(1000,9999));
            $user = User::create([
                'username' => $username,
                'display_name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'user_type' => 'user',
                'gender' => $faker->randomElement(['male', 'female']),
                'status' => $faker->randomElement(['active', 'inactive']),
                'avatar' => 'avatars/' . $faker->word . '.png',
                'banner' => 'banners/' . $faker->word . '.png',
                'bio' => $faker->sentence,
                'remember_token' => null,
            ]);
            $user->assignRole('user');
        }
    }
}
