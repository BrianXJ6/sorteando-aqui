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
        $route = route('actions.auth.users.signin-web');
        $user = $this->user();

        $this->postJson($route)
            ->assertJsonIsObject()
            ->assertJsonStructure(['message', 'errors' => ['option', 'login', 'password']])
            ->assertUnprocessable();

        $this->postJson($route, [
                'option' => UserLoginOption::EMAIL,
                'login' => $user->email,
                'password' => 'password',
            ])
            ->assertJsonStructure(['redirect', 'message'])
            ->assertJson(fn (AssertableJson $json) => $json->whereAllType([
                'redirect' => 'string',
                'message' => 'string'
            ]))
            ->assertStatus(200);

        $this->actingAs($user, 'user')
            ->postJson($route)
            ->assertRedirect();
    }

    /**
     * Web actions to log out users
     *
     * @return void
     */
    public function testWebActionsToLogOutUsers(): void
    {
        $route = route('actions.auth.users.signout-web');

        $this->postJson($route)->assertUnauthorized();

        $this->actingAs($this->user(), 'user')
            ->postJson($route)
            ->assertNoContent();
    }

    /**
     * Endpoint via API to log in a user
     *
     * @return void
     */
    public function testEndpointViaApiToLogInUser(): void
    {
        $route = route('api.auth.users.signin-api');
        $user = $this->user();

        $this->postJson($route)
            ->assertUnprocessable()
            ->assertJsonIsObject()
            ->assertJsonStructure(['message','errors' => ['option', 'login', 'password']]);

        $this->postJson($route, [
                'option' => UserLoginOption::EMAIL,
                'login' => $user->email,
                'password' => 'password',
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
                'token' => 'string',
                'message' => 'string',
            ]))
            ->assertStatus(200);

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
        $route = route('api.auth.users.signout-api');

        $this->postJson($route)
            ->assertUnauthorized();

        Sanctum::actingAs($this->user());
        $this->withoutExceptionHandling()
            ->postJson($route)
            ->assertNoContent();
    }
}
