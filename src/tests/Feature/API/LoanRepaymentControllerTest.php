<?php

namespace Tests\Feature\API;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoanRepaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();

        User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('test123456')
        ]);

        //create first loan
        Sanctum::actingAs(
            User::where('email','user@test.com')->first(),
        );

        $this->json('POST',config('app.url').'/api/loans', [
            'amount_required' => 10000,
            'loan_term' => 2
        ],['Accept' => 'application/json']);
    }

    public function test_throw_error_if_amount_field_is_empty_at_loan_repayment()
    {
        Sanctum::actingAs(
            User::where('email','user@test.com')->first(),
        );
        $loan = Loan::first();

        $response = $this->json('POST',
            config('app.url').'/api/loans/'.$loan->id.'/do_repayment/',
            ['amount' => ''],
            ['Accept' => 'application/json']);

        $response->assertStatus(422)
            ->assertJsonPath('errors.amount.0','The amount field is required.');
    }

    public function test_throw_loan_not_found()
    {
        Sanctum::actingAs(
            User::where('email','user@test.com')->first(),
        );

        $response = $this->json('POST',
            config('app.url').'/api/loans/1111/do_repayment/',
            ['amount' => 123],
            ['Accept' => 'application/json']);

        $response->assertStatus(404)
            ->assertJson(['message' => 'Your loan is not found']);
    }

    public function test_throw_your_loan_amount_is_not_enough()
    {
        Sanctum::actingAs(
            User::where('email','user@test.com')->first(),
        );

        $loan = Loan::first();

        $response = $this->json('POST',
            config('app.url')."/api/loans/$loan->id/do_repayment/",
            ['amount' => 100],
            ['Accept' => 'application/json']);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Your amount is not enough']);
    }

    public function test_can_do_repayment_succesfully()
    {
        Sanctum::actingAs(
            User::where('email','user@test.com')->first(),
        );

        $loan = Loan::first();

        $response = $this->json('POST',
            config('app.url')."/api/loans/$loan->id/do_repayment/",
            ['amount' => 5000],
            ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Repayment has been done'])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'amount_required',
                    'loan_term',
                    'status',
                    'loan_repayments' => [
                        '*' => [
                            'loan_id',
                            'amount',
                            'payment_date',
                            'status'
                        ]
                    ]
                ]
            ]);
    }
}
