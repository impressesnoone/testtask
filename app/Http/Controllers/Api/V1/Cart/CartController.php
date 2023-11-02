<?php

namespace App\Http\Controllers\Api\V1\Cart;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function show ($cart_id)
    {
        $cart = Cart::find($cart_id)->first();
        if (!$cart){
            return response()->json([
                'error' => [
                    'msg' => 'Корзина не найден',
                ]
            ],404);
        }
        return response()->json([
            'cart' => $cart->products
        ],404);
    }

    public function add($cart_id, $product_slug, Request $request)
    {
        $user = auth('sanctum')->user();
        $email = null;
        if ($user) {
            $email = $user->email;
        } else {
            $validateUser = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                ],
                [
                    'email.required' => 'Email обязателен для заполнения',
                    'email.email' => 'Введите корректный email',
                ]
            );
            if ($validateUser->fails()) {
                return response()->json([
                    'error' => [
                        'code' => 422,
                        'message' => 'Validation Error',
                        'errors' => $validateUser->messages(),
                    ]
                ], 422);
            }
            $email = $request->email;
        }
        $product = Product::where('slug', $product_slug)->first();
        if (!$product){
            return response()->json([
                'error' => [
                    'msg' => 'Товар не найден'
                ]
            ],404);
        }
        $cart = Cart::find($cart_id);
        $count = 1;
        if (empty($cart->id)) {
            // если корзина еще не существует — создаем объект
            $cart = Cart::create(['email' => $email]);
        }
        if ($cart->products->contains($product->id)) {
            // если такой товар есть в корзине — изменяем кол-во
            $pivotRow = $cart->products()->where('product_id', $product->id)->first()->pivot;
            $count = $pivotRow->count + $count;
            $pivotRow->update(['count' => $count]);
        } else {
            // если такого товара нет в корзине — добавляем его
            $cart->products()->attach($product->id, ['count' => $count]);
        }
        return response()->json([
            'cart' => [
                'id' => $cart->id,
                'products' => $cart->products()->get()
            ]
        ],201);
    }

    public function reduce($cart_id, $product_slug) {
        $cart = Cart::find($cart_id);
        $product = Product::where('slug', $product_slug)->first();
        if (!$cart){
            return response()->json([
                'error' => [
                    'msg' => 'Корзина не найден',
                ]
            ],404);
        }
        if (!$product){
            return response()->json([
                'error' => [
                    'msg' => 'Товар не найден',
                ]
            ],404);
        }
        if (!$cart->products->contains($product->id)){
            return response()->json([
                'error' => [
                    'msg' => 'Товара в корзине не найдено',
                ]
            ],404);
        }
        // если товар есть в корзине — изменяем кол-во
        if ($cart->products->contains($product->id)) {
            $pivotRow = $cart->products()->where('product_id', $product->id)->first()->pivot;
            $count = $pivotRow->count - 1;
            if ($count >= 1) {
                $pivotRow->update(['count' => $count]);
            } else {
                $pivotRow->delete();
            }
        }
        return response()->json([
            'cart' => [
                'id' => $cart->id,
                'products' => $cart->products()->get()
            ]
        ],201);
    }
    public function destroy($cart_id, $product_slug) {
        $cart = Cart::find($cart_id);
        $product = Product::where('slug', $product_slug)->first();
        if (!$cart){
            return response()->json([
                'error' => [
                    'msg' => 'Корзина не найден',
                ]
            ],404);
        }
        if (!$product){
            return response()->json([
                'error' => [
                    'msg' => 'Товар не найден',
                ]
            ],404);
        }
        if (!$cart->products->contains($product->id)){
            return response()->json([
                'error' => [
                    'msg' => 'Товара в корзине не найдено',
                ]
            ],404);
        }
        $cart->products()->detach($product->id);
        return response()->json([
            'error' => [
                'msg' => 'Товар успешно удален',
            ]
        ],201);
    }
}
