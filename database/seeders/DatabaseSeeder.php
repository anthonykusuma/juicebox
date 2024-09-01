<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Anthony',
            'email' => 'hi@anthonykusuma.com',
            'password' => bcrypt('Pswd1234!'),
        ]);

        Post::factory(18)->create(
            ['user_id' => User::first()->id]
        );
    }
}
