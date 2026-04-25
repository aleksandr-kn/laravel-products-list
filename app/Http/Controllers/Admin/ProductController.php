<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        $products = Product::with('category')->paginate(15);

        return Inertia::render('Admin/Products/Index', [
            'products' => ProductResource::collection($products),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'categories' => Category::all(['id', 'name']),
        ]);
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'product'    => new ProductResource($product->load('category')),
            'categories' => Category::all(['id', 'name']),
        ]);
    }
}
