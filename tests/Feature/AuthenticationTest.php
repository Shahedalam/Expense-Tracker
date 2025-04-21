<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
uses(RefreshDatabase::class);

function registerUser($context)
{
    $payload = [
        'name' => 'Test user',
        'email' => 'dad@dd.dawd',
        'password'=> '12345789',
        "password_confirmation"=>'12345789'
    ];

    $response = $context->postJson('/api/v1/auth/register', $payload);
    return $response->json();
}


it('get error if params email does not match rules', function () {
    $payload = [
        'name' => 'Test user',
        'email' => 'dad.dawd',
        'password'=> '12345678',
        "password_confirmation"=>'12345678'

    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);
    $data = $response->json();
    $response->assertStatus(400);
    expect($data['errors']['email'][0])->toBe("The email field must be a valid email address.");
});
it('get error if params password does not match rules', function () {
    $payload = [
        'name' => 'Test user',
        'email' => 'dad@dd.dawd',
        'password'=> '12345',
        "password_confirmation"=>'12345'
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);
    $data = $response->json();
    $response->assertStatus(400);
    expect($data['errors']['password'][0])->toBe("The password field must be at least 6 characters.");
});
it('get error if params password_confirmation missing', function () {
    $payload = [
        'name' => 'Test user',
        'email' => 'dad@dd.dawd',
        'password'=> '12345789',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);
    $data = $response->json();
    $response->assertStatus(400);
    expect($data['errors']['password'][0])->toBe("The password field confirmation does not match.");
});
it('get success if params all field follow rule', function () {
    $payload = [
        'name' => 'Test user',
        'email' => 'dad@dd.dawd',
        'password'=> '12345789',
        "password_confirmation"=>'12345789'
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);
    $data = $response->json();
//    dd($data);
    $response->assertStatus(200);
    expect($data['message'])->toBe("User Created Successfully");
});

it('get login error if email invalid', function () {
    registerUser($this);
    $loginPayload = [
        'email' => 'daddd.dawd',
        'password'=> '12345789',
    ];

    $response = $this->postJson('/api/v1/auth/login', $loginPayload);
    $data = $response->json();
    $response->assertStatus(400);
    expect($data['errors']['email'][0])->toBe("The email field must be a valid email address.");
});

it('get login error if Password invalid', function () {
    registerUser($this);
    $loginPayload = [
        'email' => 'dad@dd.dawd',
        'password'=> '123457899',
    ];

    $response = $this->postJson('/api/v1/auth/login', $loginPayload);
    $data = $response->json();
    $response->assertStatus(400);
    expect($data['message'])->toBe("Login Credentials does not match.");
});
