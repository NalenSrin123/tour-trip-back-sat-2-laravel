<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\TourSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourScheduleApiTest extends TestCase
{
    use RefreshDatabase;

    private function createTour(): Tour
    {
        return Tour::create([
            'title' => 'Test Tour',
            'description' => 'A wonderful test tour',
            'price' => 150.00,
            'duration_days' => 3,
            'max_participants' => 20,
            'status' => 'active',
        ]);
    }

    public function test_can_list_tour_schedules(): void
    {
        $tour = $this->createTour();
        TourSchedule::create([
            'tour_id' => $tour->tour_id,
            'tour_date' => '2026-10-01',
            'start_time' => '08:00',
            'available_seats' => 15,
            'price' => 120.00,
        ]);

        $response = $this->getJson('/api/tour-schedules');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    public function test_can_create_tour_schedule(): void
    {
        $tour = $this->createTour();

        $payload = [
            'tour_id' => $tour->tour_id,
            'tour_date' => '2026-10-15',
            'start_time' => '09:30',
            'available_seats' => 10,
            'price' => 99.50,
        ];

        $response = $this->postJson('/api/tour-schedules', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'tour_id' => $tour->tour_id,
                     'available_seats' => 10,
                 ]);

        $this->assertDatabaseHas('tour_schedules', [
            'tour_id' => $tour->tour_id,
            'tour_date' => '2026-10-15',
        ]);
    }

    public function test_cannot_create_tour_schedule_with_invalid_data(): void
    {
        $response = $this->postJson('/api/tour-schedules', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['tour_id', 'tour_date', 'start_time', 'available_seats', 'price']);
    }

    public function test_can_show_tour_schedule(): void
    {
        $tour = $this->createTour();
        $schedule = TourSchedule::create([
            'tour_id' => $tour->tour_id,
            'tour_date' => '2026-10-01',
            'start_time' => '08:00',
            'available_seats' => 15,
            'price' => 120.00,
        ]);

        $response = $this->getJson('/api/tour-schedules/' . $schedule->schedule_id);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'schedule_id' => $schedule->schedule_id,
                     'tour_id' => $tour->tour_id,
                 ]);
    }

    public function test_can_update_tour_schedule(): void
    {
        $tour = $this->createTour();
        $schedule = TourSchedule::create([
            'tour_id' => $tour->tour_id,
            'tour_date' => '2026-10-01',
            'start_time' => '08:00',
            'available_seats' => 15,
            'price' => 120.00,
        ]);

        $payload = [
            'available_seats' => 5,
            'price' => 135.00,
        ];

        $response = $this->putJson('/api/tour-schedules/' . $schedule->schedule_id, $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'available_seats' => 5,
                 ]);

        $this->assertDatabaseHas('tour_schedules', [
            'schedule_id' => $schedule->schedule_id,
            'available_seats' => 5,
        ]);
    }

    public function test_can_delete_tour_schedule(): void
    {
        $tour = $this->createTour();
        $schedule = TourSchedule::create([
            'tour_id' => $tour->tour_id,
            'tour_date' => '2026-10-01',
            'start_time' => '08:00',
            'available_seats' => 15,
            'price' => 120.00,
        ]);

        $response = $this->deleteJson('/api/tour-schedules/' . $schedule->schedule_id);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Tour schedule deleted successfully',
                 ]);

        $this->assertDatabaseMissing('tour_schedules', [
            'schedule_id' => $schedule->schedule_id,
        ]);
    }
}
