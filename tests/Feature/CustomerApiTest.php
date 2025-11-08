<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Customer;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_can_create_a_customer(): void
    {
        $response = $this->postJson('/api/customers', [
            'name' => 'João da Silva',
            'email' => 'joaosilva@example.com',
            'phone' => '(11) 91234-5678',
            'birthdate' => '1990-01-01',
            'address' => 'Avenida Paulista, 1000',
            'address_line_two' => 'Apto 4B',
            'neighborhood' => 'Cerqueira César',
            'zip_code' => '12345-030',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('customers', [
            'email' => 'joaosilva@example.com',
        ]);
    }

    public function test_it_can_get_a_customer(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->getJson("/api/customers/{$customer->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $customer->id,
                     'email' => $customer->email,
                 ]);
    }

    public function test_it_can_list_customers(): void
    {
        Customer::factory()->count(3)->create();

        $response = $this->getJson('/api/customers');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_it_can_update_a_customer(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->putJson("/api/customers/{$customer->id}", [
            'name' => 'Maria Oliveira',
            'email' => 'mariaoliveira@example.com',
            'phone' => '(21) 99876-5432',
            'birthdate' => '1985-05-15',
            'address' => 'Rua das Flores, 200',
            'address_line_two' => 'Casa',
            'neighborhood' => 'Jardim Botânico',
            'zip_code' => '54321-098',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'email' => 'mariaoliveira@example.com',
        ]);
    }

    public function test_it_can_delete_a_customer(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->deleteJson("/api/customers/{$customer->id}");

        $response->assertStatus(204);

        // Verifica se o registro ainda existe, mas está marcado como deletado
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'deleted_at' => now(),
        ]);
    }

    public function test_it_validates_required_fields_on_create(): void
    {
        $response = $this->postJson('/api/customers', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'name',
                     'email',
                     'phone',
                     'birthdate',
                     'address',
                     'address_line_two',
                     'neighborhood',
                     'zip_code',
                 ]);
    }

    public function test_it_validates_valid_email_format(): void {
        $response = $this->postJson('/api/customers', [
            'name' => 'Invalid Email',
            'email' => 'invalid-email-format',
            'phone' => '(11) 91234-5678',
            'birthdate' => '1990-01-01',
            'address' => 'Avenida Paulista, 1000',
            'address_line_two' => 'Apto 4B',
            'neighborhood' => 'Cerqueira César',
            'zip_code' => '12345-030',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_it_validates_unique_email_on_create(): void
    {
        Customer::factory()->create([
            'email' => 'joaosilva@example.com',
        ]);

        $response = $this->postJson('/api/customers', [
            'name' => 'João da Silva',
            'email' => 'joaosilva@example.com',
            'phone' => '(11) 91234-5678',
            'birthdate' => '1990-01-01',
            'address' => 'Avenida Paulista, 1000',
            'address_line_two' => 'Apto 4B',
            'neighborhood' => 'Cerqueira César',
            'zip_code' => '12345-030',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    // it can paginate customers
    public function test_it_can_paginate_customers(): void
    {
        Customer::factory()->count(15)->create();

        $response = $this->getJson('/api/customers?page=2&per_page=5');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data');
    }

}
