<?php

namespace Tests\Feature;

use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_tours()
    {
        Tour::create(['name' => 'Test Tour', 'description' => 'Desc']);

        $response = $this->getJson('/api/tours');

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Test Tour']);
    }

    public function test_can_create_tour()
    {
        $payload = ['name' => 'New Tour', 'description' => 'New Desc'];

        $response = $this->postJson('/api/tours', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'New Tour']);
        
        $this->assertDatabaseHas('tours', ['name' => 'New Tour']);
    }

    public function test_can_update_tour()
    {
        $tour = Tour::create(['name' => 'Old Tour', 'description' => 'Old Desc']);

        $payload = ['name' => 'Updated Tour', 'description' => 'Updated Desc'];

        $response = $this->putJson('/api/tours/' . $tour->tour_id, $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Updated Tour']);

        $this->assertDatabaseHas('tours', ['name' => 'Updated Tour', 'tour_id' => $tour->tour_id]);
    }

    public function test_can_delete_tour()
    {
        $tour = Tour::create(['name' => 'Delete Tour', 'description' => 'Desc']);

        $response = $this->deleteJson('/api/tours/' . $tour->tour_id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tours', ['tour_id' => $tour->tour_id]);
    }
}
