<?php

namespace Database\Seeders;

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
        // On crée le Super-Administrateur avec les valeurs EXACTES de l'ENUM
        User::create([
            'name' => 'Super Administrateur',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'superadmin', // Correction ici : doit correspondre à l'ENUM
            'provider' => 'simple'
        ]);
    }
}