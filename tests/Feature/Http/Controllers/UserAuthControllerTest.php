<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Enums\UserLoginOption;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Notifications\ResetPasswordNotification;

class UserAuthControllerTest extends TestCase
{
    public const FAKE_PASS = '1234567890';

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
                'password' => self::FAKE_PASS,
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
     * Test web flow to request link to reset password
     *
     * @return void
     */
    public function testWebActionsToPasswordForgotRequest(): void
    {
        Notification::fake();
        $route = route('actions.auth.users.password.forgot.request');
        $user = $this->user();

        $this->post($route)
            ->assertRedirect()
            ->assertSessionHasErrors(['email']);

        $this->post($route, ['email' => $user->email])
            ->assertJsonIsObject()
            ->assertJsonStructure(['message'])
            ->assertJson(fn (AssertableJson $json) => $json->whereType('message', 'string'))
            ->assertSuccessful();
        Notification::assertSentTo($user, ResetPasswordNotification::class);

        // many attempts
        $this->post($route, ['email' => $user->email])
            ->assertJsonIsObject()
            ->assertUnprocessable()
            ->assertJsonStructure(['message']);

        $this->actingAs($user, 'user')
            ->post($route)
            ->assertRedirect();
    }

    /**
     * Test web flow to reset password by requested link to reset password
     *
     * @return void
     */
    public function testWebActionsToPasswordForgotReset(): void
    {
        $route = route('actions.auth.users.password.forgot.reset');
        $user = $this->user();
        $fakeToken = '##########';
        $data = [
            'token' => $fakeToken,
            'email' => $user->email,
            'password' => self::FAKE_PASS,
            'password_confirmation' => self::FAKE_PASS,
        ];

        $this->post($route)
            ->assertRedirect()
            ->assertSessionHasErrors(['token', 'email', 'password']);

        $this->post($route, array_merge($data, ['password' => self::FAKE_PASS . $fakeToken]))
            ->assertRedirect()
            ->assertSessionHasErrors(['password']);

        $this->post($route, $data)
            ->assertJsonIsObject()
            ->assertUnprocessable()
            ->assertJsonStructure(['message']);

        $newPassword = '@FakePass123';
        $newToken = Password::createToken($user);
        $this->post($route, array_merge($data, [
                'token' => $newToken,
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]))
            ->assertJsonIsObject()
            ->assertJsonStructure(['redirect', 'message'])
            ->assertJson(fn (AssertableJson $json) => $json->whereAllType([
                'redirect' => ['string'],
                'message' => ['string'],
            ]))
            ->assertSuccessful();

        $this->assertTrue(Hash::check($newPassword, User::find($user->id)->password));
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
                'password' => self::FAKE_PASS,
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
     * Test API flow to request link to reset password
     *
     * @return void
     */
    public function testEndpointViaApiToPasswordForgotRequest(): void
    {
        Notification::fake();
        $route = route('api.auth.users.password.forgot.request');
        $user = $this->user();

        $this->postJson($route)
            ->assertUnprocessable()
            ->assertJsonIsObject()
            ->assertJsonStructure(['message', 'errors' => ['email']])
            ->assertJson(fn (AssertableJson $json) => $json->whereAllType([
                'message' => 'string',
                'errors.email' => 'array',
            ]));

        $this->postJson($route, ['email' => $user->email])
            ->assertJsonIsObject()
            ->assertJsonStructure(['message'])
            ->assertJson(fn (AssertableJson $json) => $json->whereType('message', 'string'))
            ->assertSuccessful();
        Notification::assertSentTo($user, ResetPasswordNotification::class);

        // many attempts
        $this->postJson($route, ['email' => $user->email])
            ->assertUnprocessable()
            ->assertJsonIsObject()
            ->assertJsonStructure(['message'])
            ->assertJson(fn (AssertableJson $json) => $json->whereType('message', 'string'));

        Sanctum::actingAs($this->user());
        $this->postJson($route)->assertRedirect();
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
