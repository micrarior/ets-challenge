<?php

namespace Tests\Feature;

use App\Company;
use App\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase, WithFaker;

    /**
     *
     * @return void
     */
    public function testCreateUser()
    {
        $name = $this->faker->name;
        $response = $this->postJson('/api/v1/users', ['name' => $name]);

        $response
            ->assertStatus(201)
            ->assertJsonFragment(['name' => $name]);

        $this->assertDatabaseHas('users', ['name' => $name]);
    }

    public function testShowUser()
    {
        $user = $this->createUser();

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $user->id, 'name' => $user->name]);

    }

    public function testDeleteUser()
    {
        $user = $this->createUser();

        $response = $this->deleteJson("/api/v1/users/{$user->id}");

        $response
            ->assertStatus(200)
            ->assertJsonFragment(['name' => $user->name]);

        $this->assertNotNull($user->fresh()->deleted_at);
    }

    public function testUpdateUser()
    {
        $user = $this->createUser();
        $newName = $this->faker->unique()->name;

        $response = $this->putJson("/api/v1/users/{$user->id}", ['name' => $newName]);

        $response
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $user->id, 'name' => $newName]);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $newName]);
    }

    public function testAttachCompanies()
    {
        $user = $this->createUser();
        $companies = factory(Company::class, 3)->create();

        $response = $this->putJson("/api/v1/users/{$user->id}/companies", ['companies' => $companies->pluck('id')]);

        $response
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $user->id]);

        foreach ($companies as $company) {
            $this->assertDatabaseHas('user_company', ['user_id' => $user->id, 'company_id' => $company->id]);
        }
    }

    /**
     * @return mixed
     */
    private function createUser()
    {
        $user = factory(User::class)->create();
        return $user;
    }
}
