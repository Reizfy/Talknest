<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $rawUsername = preg_replace('/[^a-zA-Z0-9]/', '', $this->faker->unique()->userName);
        $username = strtolower($rawUsername . rand(1000,9999));
        $status = $this->faker->numberBetween(0,2);
        switch ($status) {
            case 1:
                $status = 'active';
                break;
            case 2:
                $status = 'inactive';
                break;
            default:
                $status = 'pending';
                break;
        }
        return [
            'username' => $username,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'user_type' => 'user',
            'status' => $status,
            'gender' => $this->faker->randomElement(['male', 'female']),
        ];
    }
}
