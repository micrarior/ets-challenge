<?php

namespace Tests\Feature;

use App\Company;
use App\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase, WithFaker;

    /**
     *
     * @return void
     */
    public function testCreateCompany()
    {
        $name = $this->faker->unique()->name;
        $response = $this->postJson('/api/v1/companies', ['name' => $name]);

        $response
            ->assertStatus(201)
            ->assertJsonFragment(['name' => $name]);

        $this->assertDatabaseHas('companies', ['name' => $name]);
    }

    public function testShowCompany()
    {
        $company = $this->createCompany();

        $response = $this->getJson("/api/v1/companies/{$company->id}");

        $response
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $company->id, 'name' => $company->name]);

    }

    public function testDeleteCompany()
    {
        $company = $this->createCompany();

        $response = $this->deleteJson("/api/v1/companies/{$company->id}");

        $response
            ->assertStatus(200)
            ->assertJsonFragment(['name' => $company->name]);

        $this->assertNotNull($company->fresh()->deleted_at);
    }

    public function testUpdateCompany()
    {
        $company = $this->createCompany();
        $newName = $this->faker->unique()->company;

        $response = $this->putJson("/api/v1/companies/{$company->id}", ['name' => $newName]);

        $response
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $company->id, 'name' => $newName]);

        $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => $newName]);
    }

    public function testAttachUsers()
    {
        $company = $this->createCompany();
        $users = factory(User::class, 3)->create();

        $response = $this->putJson("/api/v1/companies/{$company->id}/users", ['users' => $users->pluck('id')]);

        $response
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $company->id]);

        foreach ($users as $user) {
            $this->assertDatabaseHas('user_company', ['user_id' => $user->id, 'company_id' => $company->id]);
        }
    }
    
    /**
     * @return mixed
     */
    private function createCompany()
    {
        $company = factory(Company::class)->create();
        return $company;
    }
}
