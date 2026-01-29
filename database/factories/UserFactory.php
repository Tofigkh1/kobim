<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

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
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // or use bcrypt('password')
            'remember_token' => Str::random(10),
            'fullName' => $this->faker->name(),
            'education' => $this->faker->randomDigit(),
            'mainWorkplace' => $this->faker->company(),
            'duty' => $this->faker->jobTitle(),
            'contactEmail' => $this->faker->unique()->safeEmail(),
            'contactNumber' => $this->faker->phoneNumber(),
            'city' => $this->faker->city(),
            'address' => $this->faker->address(),
            'date' => $this->faker->date(),
            'serviceName' => $this->faker->word(),
            'duration' => $this->faker->word(),
            'serviceForm' => $this->faker->numberBetween(0, 1),
            'status' => $this->faker->numberBetween(0, 1),
            'serviceType' => $this->faker->word(),
            'note' => $this->faker->text(),
            'user_id' => null,
            'visibility'=>1,
            'trailer' => $this->faker->url(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
