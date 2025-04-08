<?php

namespace Database\Factories;

use App\Models\ProviderProfile;
use App\Models\User;
use App\Models\WorkingHour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Provider_Profiles>
 */
class ProviderProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(['role' => 'provider'])->id,
            'service_name' => fake()->jobTitle(),
            'description' => fake()->sentence(),
            'address' => fake()->address(),
            'website' => fake()->domainName(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (ProviderProfile $provider) {
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

            foreach ($days as $day) {
                $isWorkingDay = ! in_array($day, ['Saturday', 'Sunday']);

                WorkingHour::create([
                    'provider_id' => $provider->id,
                    'day' => $day,
                    'is_working_day' => $isWorkingDay,
                    'open_time' => $isWorkingDay ? '08:00' : null,
                    'close_time' => $isWorkingDay ? '16:00' : null,
                ]);
            }
        });
    }
}
