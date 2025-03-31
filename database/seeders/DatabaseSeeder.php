<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Usuário 1',
            'email' => 'user1@email.com',
            'cpf_cnpj' => '11111111111',
            'password' => bcrypt('123456'),
            'type' => 'common',
            'balance' => 1000,
        ]);
    
        User::create([
            'name' => 'Usuário 2',
            'email' => 'user2@email.com',
            'cpf_cnpj' => '22222222222',
            'password' => bcrypt('123456'),
            'type' => 'common',
            'balance' => 200,
        ]);
    
        User::create([
            'name' => 'Usuário 3',
            'email' => 'user3@email.com',
            'cpf_cnpj' => '33333333333',
            'password' => bcrypt('123456'),
            'type' => 'shopkeeper',
            'balance' => 0,
        ]);
    
        User::create([
            'name' => 'Usuário 4',
            'email' => 'user4@email.com',
            'cpf_cnpj' => '44444444444',
            'password' => bcrypt('123456'),
            'type' => 'common',
            'balance' => 50,
        ]);
    }
}    