<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Enums\UserLoginOption;
use Illuminate\Testing\Fluent\AssertableJson;

class UserAuthControllerTest extends TestCase
{
    /**
     * Testing the WEB Sing in endpoint for users
     *
     * @return void
     */
    public function testWebActionsToAuthenticateGuestUsers(): void
    {
        $route = route('actions.auth.users.signin');
        $user = $this->user();

        $this->post($route)
            ->assertRedirect()
            ->assertSessionHasErrors(['option', 'login', 'password']);

        $this->post($route, [
                'option' => UserLoginOption::EMAIL->value,
                'login' => $user->email,
                'password' => '1234567890',
            ])
            ->assertJson(fn (AssertableJson $json) => $json->whereAllType([
                'redirect' => 'string',
                'message' => 'string'
            ]))
            ->assertJsonStructure(['redirect', 'message'])
            ->assertSuccessful();


        $this->actingAs($user, 'user')
            ->post($route)
            ->assertRedirect();
    }

    /**
     * Web actions to log out users
     *
     * @return void
     */
    public function testWebActionsToLogOutUsers(): void
    {
        $route = route('actions.auth.users.signout');

        $this->post($route)->assertRedirect();

        $this->actingAs($this->user(), 'user')
            ->post($route)
            ->assertNoContent();
    }

    /**
     * Endpoint via API to log in a user
     *
     * @return void
     */
    public function testEndpointViaApiToLogInUser(): void
    {
        $route = route('api.auth.users.signin');
        $user = $this->user();

        $this->postJson($route)
            ->assertUnprocessable()
            ->assertJsonIsObject()
            ->assertJsonStructure(['message','errors' => ['option', 'login', 'password']]);

        $this->postJson($route, [
                'option' => UserLoginOption::EMAIL->value,
                'login' => $user->email,
                'password' => '1234567890',
            ])
            ->assertJsonIsObject()
            ->assertJsonStructure(['user', 'token', 'message'])
            ->assertJson(fn (AssertableJson $json) => $json->whereAllType([
                'user.id' => 'integer',
                'user.name' => 'string',
                'user.email' => 'string',
                'user.avatar' => 'null|string',
                'user.email_verified_at' => 'string|datetime',
                'user.last_login' => 'string|datetime',
                'token' => 'null|string',
                'message' => 'string',
            ]))
            ->assertSuccessful();

        $this->actingAs($user, 'user')
            ->postJson($route)
            ->assertRedirect();
    }

    /**
     * Endpoint via API to log out a user
     *
     * @return void
     */
    public function testEndpointViaApiToLogOutUser(): void
    {
        $route = route('api.auth.users.signout');

        $this->postJson($route)->assertUnauthorized();

        Sanctum::actingAs($this->user());
        $this->withoutExceptionHandling()
            ->postJson($route)
            ->assertNoContent();
    }
}
