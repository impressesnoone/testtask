<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\CartSave;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show ()
    {
        $carts_save = CartSave::where('user_id', auth()->user()->id)->get();
        return response()->json([
            'orders' => OrderResource::collection($carts_save)
        ],200);
    }

    public function store($cart_id)
    {
        $cart = Cart::find($cart_id)->first();
        $carts_copy = $cart->products()->select('product_id', 'count')->get();
        $saves_cart = [];
        $i = 0;
        foreach ($carts_copy as $cart_copy){
            $saves_cart[$i]['product_id'] = $cart_copy->product_id;
            $saves_cart[$i]['count'] = $cart_copy->count;
            $i++;
        }
        if (!$cart){
            return response()->json([
                'error' => [
                    'msg' => 'Корзина не найден',
                ]
            ],404);
        }
        $order = Order::create([
           'user_id' => auth()->user()->id
        ]);
        foreach ($saves_cart as $save_cart){
            CartSave::create([
                'order_id' => $order->id,
                'user_id' => auth()->user()->id,
                'product_id' => $save_cart['product_id'],
                'count' => $save_cart['count'],
            ]);
        }
        $cart->products()->detach();
        return response()->json([
            'order' => [
                'id' => $order->id,
            ]
        ],201);
    }
}
