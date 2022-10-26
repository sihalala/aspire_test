<?php

namespace Tests\Feature\API;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();

        User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('test123456'),
            'is_admin' => 1
        ]);

        User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('test123456')
        ]);

    }

    public function test_throw_unauthenticated()
    {
        $response = $this->json('GET',config('app.url').'/api/loans',[],['Accept' => 'application/json']);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_can_list_loans()
    {
        Sanctum::actingAs(
            User::where('email','user@test.com')->first(),
        );

        $response = $this->json('GET',config('app.url').'/api/loans',[],['Accept' => 'application/json']);

        $response->assertSuccessful()
            ->assertJson(['message' => 'Loan retrieved successfully.'])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
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
                ]
            ]);
    }

    public function test_raise_validation_error_when_required_fields_missing()
    {
        Sanctum::actingAs(
            User::where('email','user@test.com')->first(),
        );

        $response = $this->json('POST',config('app.url').'/api/loans', [
            'amount_required' => '',
            'loan_term' => ''
        ],['Accept' => 'application/json']);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => [
                'amount_required',
                'loan_term'
            ]]);
    }

    public function test_can_create_loan()
    {
        Sanctum::actingAs(
            User::where('email','user@test.com')->first(),
        );

        $response = $this->json('POST',config('app.url').'/api/loans', [
            'amount_required' => 10000,
            'loan_term' => 2
        ],['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Loan has been created'])
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

    public function test_normal_user_cannot_approve_loan()
    {
        $this->test_can_create_loan();

        Sanctum::actingAs(
            User::where('email','user@test.com')->first(),
        );

        $loan = Loan::first();

        $response = $this->json('POST',config('app.url')."/api/loans/$loan->id/approve",[],['Accept' => 'application/json']);

        $response->assertStatus(401);
    }

    public function test_admin_can_approve_loan()
    {
        $this->test_can_create_loan();

        Sanctum::actingAs(
            User::where('email','admin@test.com')->first(),
        );

        $loan = Loan::first();

        $response = $this->json('POST',config('app.url')."/api/loans/$loan->id/approve",[],['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Loan has been approved successfully.'])
            ->assertJsonPath('data.status','APPROVED');
    }

}
