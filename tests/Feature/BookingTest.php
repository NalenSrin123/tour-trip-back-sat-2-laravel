<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_bookings()
    {
        $response = $this->getJson('/api/bookings');
        $response->assertStatus(200);
    }

    public function test_can_create_booking()
    {
        $user = User::factory()->create();

        $payload = [
            'user_id' => $user->id,
            'schedule_id' => 1,
            'total_amount' => 150.00,
            'status' => 'pending',
        ];

        $response = $this->postJson('/api/bookings', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Booking created successfully',
                'data' => [
                    'user_id' => $user->id,
                    'schedule_id' => 1,
                    'total_amount' => 150.00,
                    'status' => 'pending',
                ]
            ]);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'schedule_id' => 1,
            'total_amount' => 150.00,
            'status' => 'pending',
        ]);
    }

    public function test_cannot_create_booking_without_required_fields()
    {
        $response = $this->postJson('/api/bookings', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_id', 'schedule_id', 'total_amount']);
    }

    public function test_can_filter_bookings_by_status_and_user_id()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->postJson('/api/bookings', [
            'user_id' => $user1->id,
            'schedule_id' => 1,
            'total_amount' => 100,
            'status' => 'pending',
        ]);

        $this->postJson('/api/bookings', [
            'user_id' => $user2->id,
            'schedule_id' => 2,
            'total_amount' => 200,
            'status' => 'confirmed',
        ]);

        // Filter by status
        $resStatus = $this->getJson('/api/bookings?status=pending');
        $resStatus->assertStatus(200);
        $this->assertCount(1, $resStatus->json('data'));

        // Filter by user_id
        $resUser = $this->getJson("/api/bookings?user_id={$user2->id}");
        $resUser->assertStatus(200);
        $this->assertCount(1, $resUser->json('data'));
    }

}
