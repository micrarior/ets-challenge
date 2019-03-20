<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = factory(\App\Company::class, 10)->create();

        factory(\App\User::class)
            ->times(10)
            ->create()
            ->each(function($user) use ($companies) {
                $user->companies()->attach(
                    $companies->random(rand(1, 3))->pluck('id')
                );
            });
    }
}
