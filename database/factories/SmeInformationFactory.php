<?php

namespace Database\Factories;

use App\Models\SmeInformation;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmeInformationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SmeInformation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'smeName' => $this->faker->company(),
            'smeLocation' => $this->faker->address(),
            'contactNumber' => $this->faker->phoneNumber(),
            'contactEmail' => $this->faker->unique()->safeEmail(),
            'teamLeader_id' => 1,
            'teamLeaderName' => $this->faker->name(),
            'user_id' => 1,
            'executiveCompany' => $this->faker->company(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
