<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Поиск товаров по названию
     *
     * Регистронезависимый поиск через ilike (PostgreSQL)
     * Игнорирует запрос короче 2 и длиннее 100 символов
     * Экранирует спецсимволы LIKE (% и _) для защиты от подстановки
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $search - поисковая строка от пользователя
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, ?string $search)
    {
        $search = $search ? trim($search) : null;

        if (!$search || mb_strlen($search) < 2 || mb_strlen($search) > 100) {
            return $query;
        }

        // экранируем спецсимволы LIKE чтобы % и _ искались как обычные символы
        $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $search);

        return $query->where('name', 'ilike', "%{$escaped}%");
    }
}
