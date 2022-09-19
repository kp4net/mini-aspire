<?php

namespace Tests\Feature\Api\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;


    public function setUp(): void
    {
        parent::setUp();

        // create a user
        User::factory()->create([
            'email' => 'johndoe@example.org',
            'password' => Hash::make('testpassword')
        ]);
    }

    public function test_show_validation_error_when_both_fields_empty()
    {

        $response = $this->json('POST', route('auth.login'), [
            'email' => '',
            'password' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonValidationErrors(['email', 'password']);
    }


    public function test_show_validation_error_on_email_when_credential_donot_match()
    {
        $response = $this->json('POST', route('auth.login'), [
            'email' => 'test@test.com',
            'password' => 'abcdabcd'
        ]);

        $response->assertStatus(401);
    }

    public function test_return_user_and_access_token_after_successful_login()
    {
        $response = $this->json('POST', route('auth.login'), [
            'email' => 'johndoe@example.org',
            'password' => 'testpassword',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);
    }

    public function test_non_authenticated_user_cannot_get_user_details()
    {

        $response = $this->json('GET', route('auth.user'));

        $response->assertStatus(401)
            ->assertSee('Unauthenticated');
    }

    public function test_authenticated_user_can_get_user_details()
    {
        Sanctum::actingAs(
            User::first(),
        );

        $response = $this->json('GET', route('auth.user'));

        $response->assertStatus(200)
            ->assertJsonStructure(['name', 'email']);
    }

    public function test_non_authenticated_user_cannot_logout()
    {
        $response = $this->json('POST', route('auth.logout'), []);

        $response->assertStatus(401)
            ->assertSee('Unauthenticated');;
    }

    public function test_authenticated_user_can_logout()
    {
        Sanctum::actingAs(
            User::first(),
        );

        $response = $this->json('POST', route('auth.logout'), []);

        $response->assertStatus(200);
    }
}
