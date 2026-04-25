<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверяем что POST /api/login с верными данными возвращает токен
     * Токен нужен для доступа к защищенным эндпоинтам
     */
    public function test_login_returns_token(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    /**
     * Проверяем что неверный пароль возвращает 401
     * API не должен раскрывать существует ли email в системе
     */
    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(401);
    }
}
