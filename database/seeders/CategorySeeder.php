<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Электроника', 'description' => 'Смартфоны, ноутбуки, планшеты и аксессуары'],
            ['name' => 'Одежда', 'description' => 'Мужская, женская и детская одежда'],
            ['name' => 'Обувь', 'description' => 'Кроссовки, ботинки, сандалии'],
            ['name' => 'Книги', 'description' => 'Художественная литература, учебники, журналы'],
            ['name' => 'Спорт', 'description' => 'Инвентарь, одежда и аксессуары для спорта'],
            ['name' => 'Дом и сад', 'description' => 'Мебель, декор, садовый инструмент'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
