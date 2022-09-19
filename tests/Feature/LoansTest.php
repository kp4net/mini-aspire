<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoansTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_direct_access_loans()
    {
        $response = $this->get('/loans');

        $response->assertStatus(404);

        $response = $this->post('loans', [
            'amount' => 1000,
            'term' => 3
        ]);

        $response->assertStatus(404);

        $response = $this->get('/loans/1');

        $response->assertStatus(404);

        $response = $this->post('/repayment/1');

        $response->assertStatus(404);

        $response = $this->post('/approveLoan/1');

        $response->assertStatus(404);
    }
}
