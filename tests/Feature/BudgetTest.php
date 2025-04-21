<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
uses(RefreshDatabase::class);
it('get error if Create Budget params invalid', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $budgetPayload = [
        'month' => '2025-04-0',
        'budget'=> '-1',
    ];
    $response = $this->postJson('/api/v1/budget', $budgetPayload);
    $data = $response->json();
    $response->assertStatus(400);
    expect($data['message'])->toBe("validation error");
});

it('get 201 response if Create Budget params valid', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $budgetPayload = [
        'month' => '2025-04-01',
        'budget'=> '10000',
    ];
    $response = $this->postJson('/api/v1/budget', $budgetPayload);
    $data = $response->json();
//    dd($data);
    $response->assertStatus(201);
    expect($data['message'])->toBe("Budget Created successfully");
});
it('get error if Create month params exist in db', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $this->postJson('/api/v1/budget', [
        'month' => '2025-04-01',
        'budget'=> '10000',
    ]);
    $response = $this->postJson('/api/v1/budget', [
        'month' => '2025-04-01',
        'budget'=> '12000',
    ]);
    $data = $response->json();
    $response->assertStatus(400);
    expect($data['message'])->toBe("For month April Budget exist");
});
it('get error response if Update Budget params valid', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $budgetPayload = [
        'month' => '2025-04-01',
        'budget'=> '10000',
    ];
    $response = $this->postJson('/api/v1/budget', $budgetPayload);
    $data = $response->json();

    $budgetUpdatePayload = [
        'month' => '2025-04-01',
        'budget'=> 7000,
    ];
    $updateResponse = $this->putJson('/api/v1/budget/'.$data['budget']['id'], $budgetUpdatePayload);
    $updatedData = $updateResponse->json();
//    dd($updatedData);
    $updateResponse->assertStatus(201);
    expect($updatedData['message'])->toBe("Budget updated successfully")
        ->and($updatedData['budget']['budget'])->toBe(7000);
});
it('show all budget for a user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $response = $this->postJson('/api/v1/budget', [
        'month' => '2025-04-01',
        'budget'=> '10000',
    ]);
    $response = $this->postJson('/api/v1/budget', [
        'month' => '2025-05-01',
        'budget'=> '10000',
    ]);
    $response = $this->getJson('/api/v1/budget');
    $data = $response->json();
//    dd($data);
    $response->assertStatus(200);
    expect(count($data['budget']))->toBe(2);
});
it('Delete a budget for a user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $response = $this->postJson('/api/v1/budget', [
        'month' => '2025-04-01',
        'budget'=> '10000',
    ]);
    $deleteData = $response->json();
    $response = $this->deleteJson('/api/v1/budget/'.$deleteData['budget']['id']);
    $data = $response->json();
    expect($data['message'])->toBe("Budget deleted successfully");


    $response = $this->getJson('/api/v1/budget');
    $data = $response->json();

    $response->assertStatus(200);
    expect(count($data['budget']))->toBe(0);
});

it('get Error if budget is not belongs user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $response = $this->postJson('/api/v1/budget', [
        'month' => '2025-04-01',
        'budget'=> '10000',
    ]);
    $data = $response->json();

    $userTwo = User::factory()->create();
    Sanctum::actingAs($userTwo);
    $response = $this->getJson('/api/v1/budget/'.$data['budget']['id']);
    $data = $response->json();
    $response->assertStatus(401);
    expect($data['message'])->toBe("Unauthorized, Budget not belongs to this user.");
});
