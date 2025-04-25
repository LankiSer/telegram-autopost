<?php

namespace Database\Factories;

use App\Models\GigaChatCredential;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GigaChatCredential>
 */
class GigaChatCredentialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GigaChatCredential::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => fake()->uuid(),
            'client_secret' => fake()->sha256(),
            'auth_url' => 'https://auth.gigachat.api/v1',
            'api_url' => 'https://gigachat.api/v1',
            'user_id' => User::factory(),
        ];
    }
} 