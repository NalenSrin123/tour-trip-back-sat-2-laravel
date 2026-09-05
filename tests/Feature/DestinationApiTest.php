<?php

namespace Tests\Feature;

use App\Models\Destination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_destination_can_be_updated(): void
    {
        $destination = Destination::create([
            'name' => 'Old Beach',
            'image' => 'old.jpg',
            'description' => 'Old description',
        ]);

        $response = $this->patchJson("/api/destinations/{$destination->destination_id}", [
            'name' => 'New Beach',
            'image' => null,
            'description' => 'Updated description',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Destination updated successfully.')
            ->assertJsonPath('data.destination_id', $destination->destination_id)
            ->assertJsonPath('data.name', 'New Beach')
            ->assertJsonPath('data.image', null)
            ->assertJsonPath('data.description', 'Updated description');

        $this->assertDatabaseHas('destinations', [
            'destination_id' => $destination->destination_id,
            'name' => 'New Beach',
            'image' => null,
            'description' => 'Updated description',
        ]);
    }

    public function test_destination_update_validates_name(): void
    {
        $destination = Destination::create([
            'name' => 'Old Beach',
        ]);

        $response = $this->patchJson("/api/destinations/{$destination->destination_id}", [
            'name' => '',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_destination_can_be_deleted(): void
    {
        $destination = Destination::create([
            'name' => 'Old Beach',
        ]);

        $response = $this->deleteJson("/api/destinations/{$destination->destination_id}");

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Destination deleted successfully.');

        $this->assertDatabaseMissing('destinations', [
            'destination_id' => $destination->destination_id,
        ]);
    }
}
