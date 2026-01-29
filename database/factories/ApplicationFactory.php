<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Application::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'applicationNumber' => $this->faker->unique()->numerify('APP-#####'),
            'applicationType' => $this->faker->randomElement(['Type1', 'Type2', 'Type3']),
            'voen' => $this->faker->numerify('##########'),
            'fin' => $this->faker->bothify('??######'),
            'voenPersonName' => $this->faker->name(),
            'voenMeyar' => $this->faker->word(),
            'voenAddress' => $this->faker->address(),
            'voenActivityName' => $this->faker->word(),
            'voenContactInfo' => json_encode(['phone' => $this->faker->phoneNumber(), 'email' => $this->faker->email()]),
            'voenFieldActivity' => $this->faker->word(),
            'employeeType' => $this->faker->randomElement([0, 1]),
            'employeeCount' => $this->faker->numberBetween(1, 100),
            'fullName' => $this->faker->name(),
            'education' => $this->faker->word(),
            'fieldActivity' => $this->faker->word(),
            'fieldWantAct' => $this->faker->word(),
            'otherFieldActivity' => $this->faker->word(),
            'actualResidentialAddress' => $this->faker->randomElement([0, 1]),
            'actualCity' => $this->faker->city(),
            'actualAddress' => $this->faker->address(),
            'mainPlaceWork' => $this->faker->company(),
            'duty' => $this->faker->jobTitle(),
            'contactNumber' => $this->faker->phoneNumber(),
            'contactEmail' => $this->faker->unique()->safeEmail(),
            'signatureNumber' => $this->faker->unique()->numerify('SIG-#####'),
            'city' => $this->faker->city(),
            'address' => $this->faker->address(),
            'note' => $this->faker->paragraph(),
            'user_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
