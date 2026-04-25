<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicController extends Controller
{
    public function index(Request $request): Response
    {
        $products = Product::with('category')
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->search($request->search)
            ->paginate(15);

        return Inertia::render('Public/Products/Index', [
            'products'   => ProductResource::collection($products),
            'categories' => Category::all(['id', 'name']),
        ]);
    }

    public function show(Product $product): Response
    {
        // загружаем категорию сразу, чтобы не было N+1 запросов
        return Inertia::render('Public/Products/Show', [
            'product' => new ProductResource($product->load('category')),
        ]);
    }
}
