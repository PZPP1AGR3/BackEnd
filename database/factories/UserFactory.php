<?php

namespace Database\Factories;

use App\Enum\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'username' => $this->faker->unique()->userName,
            'password' => bcrypt('test'),
            'role' => Role::User,
        ];
    }

    public function admin(): self
    {
        return $this->state(fn () => [
            'role' => Role::Admin,
        ]);
    }

    public function randomPassword(): self
    {
        return $this->state(fn () => [
            'password' => bcrypt($this->faker->password),
        ]);
    }
}
