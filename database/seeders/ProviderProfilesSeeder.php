<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProviderProfile;
use App\Models\User;

class ProviderProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'user_id' => User::where('email', 'john@example.com')->first()->id, // kapcsolat a user_id-hez
                'service_name' => 'Web Development',
                'description' => 'Full-stack web development services.',
                'profile_image' => 'profile_images/john.jpg',
            ],
            [
                'user_id' => User::where('email', 'jane@example.com')->first()->id,
                'service_name' => 'Graphic Design',
                'description' => 'Creative graphic design for branding and marketing.',
                'profile_image' => 'profile_images/jane.jpg',
            ],
        ];
        foreach ($providers as $provider) {
            ProviderProfile::create($provider);
        }
    
    }
}
