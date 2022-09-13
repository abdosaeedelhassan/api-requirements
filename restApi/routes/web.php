<?php

use App\Models\Product;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});




Route::get('products', function () {

    $products = Product::when(Request::get('category'), function ($query) {
        $query->where('category', Request::get('category'));
    })->when(Request::get('price'), function ($query) {
        $query->where('price', Request::get('price'));
    })
        ->cursor()->map(function ($product) {
            $perentage = null;
            if ($product->category == 'insurance') {
                $perentage = 30;
            } else if ($product->sku == '000003') {
                $perentage = 15;
            }
            $final = $product->price;
            if ($perentage) {
                $final = $final - $final * ($perentage / 100);
            }
            return [
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category,
                'price' => [
                    "original" => $product->price,
                    "final" => $final,
                    "discount_percentage" => $perentage ? ($perentage . '%') : null,
                    "currency" => "EUR"
                ],
            ];
        });
    return response()->json($products);
});
