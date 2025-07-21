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
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'username' => $faker->unique()->userName,
            'email' => $faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'phone_number' => $faker->phoneNumber,
            'email_verified_at' => now(),
            'user_type' => 'admin',
            'status' => 'active',
            'avatar' => 'avatars/' . $faker->word . '.png',
            'banner' => 'banners/' . $faker->word . '.png',
            'bio' => $faker->sentence,
            'remember_token' => null,
        ]);
        $admin->assignRole('admin');

        // 19 users
        for ($i = 0; $i < 19; $i++) {
            $user = User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'phone_number' => $faker->phoneNumber,
                'email_verified_at' => now(),
                'user_type' => 'user',
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
