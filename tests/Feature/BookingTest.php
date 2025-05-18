<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\ProviderProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_book_an_appointment()
    {
        $provider = ProviderProfile::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        $start = Carbon::parse('2025-05-20 10:00:00');
        $end = Carbon::parse('2025-05-20 11:00:00');

        $response = $this->post("create-booking/{$provider->id}", [
            'service_name' => 'Another Test',
            'start_time' => $start,
            'end_time' => $end,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount('appointments', 1);
        $this->assertDatabaseHas('appointments', [
            'start_time' => $start->toDateTimeString(),
            'end_time' => $end->toDateTimeString(),
        ]);
    }

    public function test_cannot_book_if_time_slot_taken()
    {
        $provider = ProviderProfile::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        $start = Carbon::parse('2025-05-20 10:00:00');
        $end = Carbon::parse('2025-05-20 11:00:00');

        Appointment::create([
            'user_id' => $user->id,
            'provider_id' => $provider->id,
            'start_time' => '2025-05-20 10:00:00',
            'end_time' => '2025-05-20 11:00:00',
            'service_name' => 'Test',
            'status' => 'pending',
        ]);
        $response = $this->post("create-booking/{$provider->id}", [
            'service_name' => 'Another Test',
            'start_time' => '2025-05-20 10:30:00',
            'end_time' => '2025-05-20 11:30:00',

        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount('appointments', 1);
        $this->assertDatabaseHas('appointments', [
            'start_time' => $start->toDateTimeString(),
            'end_time' => $end->toDateTimeString(),
        ]);

    }
}
