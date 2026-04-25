<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Админ юзер для доступа к изменяющим данные эндпойнтам
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // категории идут первыми,
        // т.к. товары ссылаются на них
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
