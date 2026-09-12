<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_customers()
    {
        Customer::create([
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'phone' => '012345678',
            'address' => 'Phnom Penh',
        ]);

        $response = $this->getJson('/api/customers');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.data.0.name', 'Alice Smith');
    }

    public function test_can_search_by_name_email_phone_address()
    {
        Customer::create([
            'name' => 'John Wick',
            'email' => 'continental@hotel.com',
            'phone' => '099999999',
            'address' => 'New York City',
        ]);

        Customer::create([
            'name' => 'Jane Doe',
            'email' => 'janedoe@gmail.com',
            'phone' => '088888888',
            'address' => 'Siem Reap',
        ]);

        // Search by name
        $resName = $this->getJson('/api/customers?search=Wick');
        $resName->assertStatus(200);
        $this->assertCount(1, $resName->json('data.data'));
        $this->assertEquals('John Wick', $resName->json('data.data.0.name'));

        // Search by email
        $resEmail = $this->getJson('/api/customers?search=continental');
        $resEmail->assertStatus(200);
        $this->assertCount(1, $resEmail->json('data.data'));
        $this->assertEquals('John Wick', $resEmail->json('data.data.0.name'));

        // Search by phone
        $resPhone = $this->getJson('/api/customers?search=0888');
        $resPhone->assertStatus(200);
        $this->assertCount(1, $resPhone->json('data.data'));
        $this->assertEquals('Jane Doe', $resPhone->json('data.data.0.name'));

        // Search by address
        $resAddress = $this->getJson('/api/customers?search=Siem');
        $resAddress->assertStatus(200);
        $this->assertCount(1, $resAddress->json('data.data'));
        $this->assertEquals('Jane Doe', $resAddress->json('data.data.0.name'));
    }

    public function test_can_filter_by_specific_email_and_phone()
    {
        Customer::create([
            'name' => 'Customer A',
            'email' => 'alpha@test.com',
            'phone' => '011111111',
            'address' => 'Address 1',
        ]);

        Customer::create([
            'name' => 'Customer B',
            'email' => 'beta@test.com',
            'phone' => '022222222',
            'address' => 'Address 2',
        ]);

        // Filter by email
        $resEmail = $this->getJson('/api/customers?email=beta');
        $resEmail->assertStatus(200);
        $this->assertCount(1, $resEmail->json('data.data'));
        $this->assertEquals('Customer B', $resEmail->json('data.data.0.name'));

        // Filter by phone
        $resPhone = $this->getJson('/api/customers?phone=0111');
        $resPhone->assertStatus(200);
        $this->assertCount(1, $resPhone->json('data.data'));
        $this->assertEquals('Customer A', $resPhone->json('data.data.0.name'));
    }

    public function test_can_sort_customers()
    {
        Customer::create(['name' => 'Charlie', 'email' => 'c@test.com', 'phone' => '333']);
        Customer::create(['name' => 'Alice', 'email' => 'a@test.com', 'phone' => '111']);
        Customer::create(['name' => 'Bob', 'email' => 'b@test.com', 'phone' => '222']);

        // Sort by name ASC
        $resAsc = $this->getJson('/api/customers?sort_by=name&sort_order=asc');
        $resAsc->assertStatus(200);
        $namesAsc = array_column($resAsc->json('data.data'), 'name');
        $this->assertEquals(['Alice', 'Bob', 'Charlie'], $namesAsc);

        // Sort by name DESC
        $resDesc = $this->getJson('/api/customers?sort_by=name&sort_order=desc');
        $resDesc->assertStatus(200);
        $namesDesc = array_column($resDesc->json('data.data'), 'name');
        $this->assertEquals(['Charlie', 'Bob', 'Alice'], $namesDesc);

        // Disallowed sort_by defaults safely to created_at
        $resSafe = $this->getJson('/api/customers?sort_by=invalid_column&sort_order=asc');
        $resSafe->assertStatus(200);
    }

    public function test_pagination_limits()
    {
        for ($i = 1; $i <= 15; $i++) {
            Customer::create([
                'name' => "Customer {$i}",
                'email' => "user{$i}@test.com",
            ]);
        }

        // Default per_page = 10
        $resDefault = $this->getJson('/api/customers');
        $resDefault->assertStatus(200);
        $this->assertEquals(10, count($resDefault->json('data.data')));
        $this->assertEquals(15, $resDefault->json('data.total'));

        // Custom per_page = 5
        $resCustom = $this->getJson('/api/customers?per_page=5');
        $resCustom->assertStatus(200);
        $this->assertEquals(5, count($resCustom->json('data.data')));
    }

    public function test_crud_endpoints()
    {
        // CREATE
        $createRes = $this->postJson('/api/customers', [
            'name' => 'David',
            'email' => 'david@test.com',
            'phone' => '077777777',
            'address' => 'Battambang',
        ]);
        $createRes->assertStatus(201);
        $customerId = $createRes->json('data.id');

        // SHOW
        $showRes = $this->getJson("/api/customers/{$customerId}");
        $showRes->assertStatus(200)
            ->assertJsonPath('data.name', 'David');

        // UPDATE
        $updateRes = $this->putJson("/api/customers/{$customerId}", [
            'name' => 'David Updated',
        ]);
        $updateRes->assertStatus(200)
            ->assertJsonPath('data.name', 'David Updated');

        // DELETE
        $deleteRes = $this->deleteJson("/api/customers/{$customerId}");
        $deleteRes->assertStatus(200);
        $this->assertDatabaseMissing('customers', ['id' => $customerId]);
    }
}
