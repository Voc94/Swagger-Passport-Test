<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ensure a user exists
        $user = User::first();

        if (!$user) {
            $user = User::create([
                'name' => 'Default User',
                'email' => 'defaultuser@example.com',
                'password' => Hash::make('password'), // Ensure the password is hashed
            ]);
        }

        $convo = Conversation::create(['name' => 'Conversatie']);
        $role = Role::firstOrCreate(['name' => 'admin']);

        // Manually insert into the three-way pivot table
        \DB::table('conversations_roles_users')->insert([
            'user_id' => $user->id,
            'conversation_id' => $convo->id,
            'role_id' => $role->id,
        ]);
    }
}
