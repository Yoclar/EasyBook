<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use App\Models\ProviderProfile;
use Carbon\Carbon;
use Tests\TestCase; 
use Illuminate\Foundation\Testing\RefreshDatabase;



class BookingApprovalAndDenial extends TestCase
{
    use RefreshDatabase;
    public function test_booking_approval()
    {
        $user = User::factory()->create();
        $provider = ProviderProfile::factory()->create();
        $this->actingAs($user); 

        $appointment = Appointment::create([
             'start_time' => Carbon::parse('2025-06-01 10:00'),
            'end_time' => Carbon::parse('2025-06-01 11:00'),
            'user_id' => $user->id,
            'provider_id' => $provider->id,
            'service_name' => 'Teszt',
            'status' => 'pending',
        ]);
        $this->actingAs($provider->user);
        $response = $this->patch("/approveApplication/{$appointment->id}");
        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed'
        ]);
    }


    public function test_booking_denial()
    {
        $user = User::factory()->create();
        $provider = ProviderProfile::factory()->create();
        $this->actingAs($user); 

        $appointment = Appointment::create([
            'start_time' => Carbon::parse('2025-06-01 10:00'),
            'end_time' => Carbon::parse('2025-06-01 11:00'),
            'user_id' => $user->id,
            'provider_id' => $provider->id,
            'service_name' => 'Teszt2',
            'status' => 'pending',
        ]);
        $this->actingAs($provider->user);
        $response = $this->patch("/declineApplication/{$appointment->id}");
        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'canceled'
        ]);


    }
}