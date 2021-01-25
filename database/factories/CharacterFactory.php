<?php

namespace Database\Factories;

use App\Models\Character;
use Illuminate\Database\Eloquent\Factories\Factory;

class CharacterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Character::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $houses = [
            '1760529f-6d51-4cb1-bcb1-25087fce5bde', '542b28e2-9904-4008-b038-034ab312ad7e',
            '56cabe3a-9bce-4b83-ba63-dcd156e9be45', 'df01bd60-e3ed-478c-b760-cdbd9afe51fc'
        ];

        return [
            'name' => $this->faker->name,
            'role' => $this->faker->text,
            'school' => $this->faker->text,
            'patronus' => $this->faker->text,
            'house' => $houses[array_rand($houses, 1)]
        ];
    }
}
