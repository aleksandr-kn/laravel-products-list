<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthUser(): User
    {
        return User::factory()->create();
    }

    private function authHeaders(User $user): array
    {
        $token = $user->createToken('test')->plainTextToken;

        return ['Authorization' => "Bearer $token"];
    }

    /**
     * Проверяем что GET /api/products возвращает список товаров
     * Создаем 3 товара и убеждаемся что API вернул все три
     * Эндпоинт публичный - токен не требуется
     */
    public function test_can_list_products(): void
    {
        $category = Category::create(['name' => 'Тестовая категория']);
        Product::factory(3)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * Проверяем что авторизованный пользователь может создать товар
     * Отправляем POST с валидными данными и Sanctum-токеном
     * Ожидаем 201, корректный JSON и наличие записи в БД
     */
    public function test_can_create_product_with_auth(): void
    {
        $user = $this->createAuthUser();
        $category = Category::create(['name' => 'Электроника']);

        $response = $this->postJson('/api/products', [
            'name' => 'Тестовый товар',
            'description' => 'Описание товара',
            'price' => 199.90,
            'category_id' => $category->id,
        ], $this->authHeaders($user));

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Тестовый товар');

        $this->assertDatabaseHas('products', ['name' => 'Тестовый товар']);
    }

    /**
     * Проверяем что без токена создание товара запрещено
     * Отправляем POST с валидными данными, но без заголовка Authorization
     * Ожидаем 401 Unauthorized
     */
    public function test_cannot_create_product_without_auth(): void
    {
        $category = Category::create(['name' => 'Электроника']);

        $response = $this->postJson('/api/products', [
            'name' => 'Тестовый товар',
            'price' => 199.90,
            'category_id' => $category->id,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Проверяем валидацию обязательных полей при создании товара
     * Отправляем пустой POST с токеном
     * Ожидаем 422 с ошибками по полям name, price и category_id
     */
    public function test_create_product_validates_required_fields(): void
    {
        $user = $this->createAuthUser();

        $response = $this->postJson('/api/products', [], $this->authHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'category_id']);
    }

    /**
     * Проверяем мягкое удаление товара авторизованным пользователем
     * После DELETE запись остается в БД с заполненным deleted_at
     * Ожидаем 204 No Content
     */
    public function test_can_delete_product_with_auth(): void
    {
        $user = $this->createAuthUser();
        $category = Category::create(['name' => 'Электроника']);
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->deleteJson("/api/products/{$product->id}", [], $this->authHeaders($user));

        $response->assertStatus(204);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
