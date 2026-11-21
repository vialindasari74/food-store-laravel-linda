<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Product;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Menampilkan semua produk
     */
    public function index()
    {
        $products = Product::query()
            ->with(['category', 'ratings.customer'])
            ->withAvg('ratings', 'rating')
            ->when(request()->search, function ($query) {
                $query->where('title', 'like', '%' . request()->search . '%');
            })
            ->paginate(10);

        $products->getCollection()->transform(function ($product) {
            $product->ratings_avg_rating = number_format(
                $product->ratings_avg_rating, 1
            );
            return $product;
        });

        return response()->json([
            'success' => true,
            'message' => 'List Produk',
            'data' => $products
        ]);
    }

    /**
     * Menampilkan produk populer
     */
    public function productsPopular()
    {
        $products = Product::query()
            ->with(['category', 'ratings.customer'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->orderByDesc('ratings_count')
            ->orderByDesc('ratings_avg_rating')
            ->paginate(10);

        $products->getCollection()->transform(function ($product) {
            $product->ratings_avg_rating = number_format(
                $product->ratings_avg_rating, 1
            );
            return $product;
        });

        return response()->json([
            'success' => true,
            'message' => 'List Produk Populer',
            'data' => $products
        ]);
    }

    /**
     * Menampilkan detail produk
     */
    public function show($slug)
    {
        $product = Product::query()
            ->with(['category', 'ratings.customer'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->where('slug', $slug)
            ->firstOrFail();

        $product->ratings_avg_rating = number_format(
            $product->ratings_avg_rating, 1
        );

        return response()->json([
            'success' => true,
            'message' => 'Detail Produk',
            'data' => $product
        ]);
    }
}
