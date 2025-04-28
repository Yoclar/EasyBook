<?php

namespace Database\Seeders;

use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProviderProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'user_id' => User::where('email', 'john@example.com')->first()->id, // kapcsolat a user_id-hez
                'company_name' => 'Web Development',
                'description' => 'Full-stack web development services.',
                'address' => '1061, Budapest, Andrássy út 39',
                'website' => 'www.fullstack.hu',
            ],
            [
                'user_id' => User::where('email', 'jane@example.com')->first()->id,
                'company_name' => 'Graphic Design',
                'description' => 'Creative graphic design for branding and marketing.',
                'address' => '4025, Debrecen, Piac utca 20',
                'website' => 'www.graphicdesign.com',
            ],
        ];
        foreach ($providers as $provider) {
            ProviderProfile::create($provider);
        }

    }
}
