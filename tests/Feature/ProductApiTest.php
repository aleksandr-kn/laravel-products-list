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

    /**
     * Проверяем обновление товара авторизованным пользователем
     * Отправляем PUT с новыми данными, убеждаемся что они сохранились
     */
    public function test_can_update_product_with_auth(): void
    {
        $user = $this->createAuthUser();
        $category = Category::create(['name' => 'Электроника']);
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Обновленный товар',
            'price' => 299.90,
        ], $this->authHeaders($user));

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Обновленный товар');
    }

    /**
     * Проверяем что удаленный товар не появляется в списке
     * SoftDeletes должен автоматически фильтровать удаленные записи
     */
    public function test_deleted_product_not_visible_in_list(): void
    {
        $category = Category::create(['name' => 'Электроника']);
        $product = Product::factory()->create(['category_id' => $category->id]);

        $product->delete();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /**
     * Проверяем что в ответе API товар содержит данные о категории
     * Eager loading через with('category') должен подгрузить связь
     */
    public function test_products_include_category(): void
    {
        $category = Category::create(['name' => 'Электроника']);
        Product::factory()->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.category.name', 'Электроника');
    }

    /**
     * Проверяем фильтрацию товаров по категории
     * При передаче category_id должны вернуться только товары этой категории
     */
    public function test_can_filter_products_by_category(): void
    {
        $electronics = Category::create(['name' => 'Электроника']);
        $books = Category::create(['name' => 'Книги']);

        Product::factory(3)->create(['category_id' => $electronics->id]);
        Product::factory(2)->create(['category_id' => $books->id]);

        $response = $this->getJson("/api/products?category_id={$electronics->id}");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}
