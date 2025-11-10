<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    public function test_it_has_fillable_attributes(): void
    {
        $fillable = [
            'name',
            'email',
            'phone',
            'birthdate',
            'address',
            'address_line_two',
            'neighborhood',
            'zip_code',
        ];

        $customer = new \App\Models\Customer;
        $this->assertEquals($fillable, $customer->getFillable());
    }

    public function test_it_uses_soft_deletes_trait(): void
    {
        $traits = class_uses(\App\Models\Customer::class);
        $this->assertContains('Illuminate\Database\Eloquent\SoftDeletes', $traits);
    }

    // There should not be two clients with the same email address.
    public function test_email_must_be_unique(): void
    {
        $customer1 = \App\Models\Customer::factory()->create(['email' => 'foobar@example.com']);
        $this->expectException(\Illuminate\Database\QueryException::class);
        $customer2 = \App\Models\Customer::factory()->create(['email' => 'foobar@example.com']);
    }
}
