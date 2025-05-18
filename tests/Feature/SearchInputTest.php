<?php

namespace Tests\Feature;


use App\Models\ProviderProfile;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchInputTest extends TestCase
{
    use RefreshDatabase;
    public function test_search_returns_expected_results()
    {
     
       ProviderProfile::factory()->create(['company_name' => 'Masszőr Mária']);
       ProviderProfile::factory()->create(['company_name' => 'Kozmetikus Kati']);
       $user = User::factory()->create();
       $this->actingAs($user);
        $response = $this->get('/providers?search=masszőr');
        
        $response->assertOk();
        $response->assertSee('Masszőr Mária');
        $response->assertDontSee('Kozmetikus Kati');
    }

   
}

