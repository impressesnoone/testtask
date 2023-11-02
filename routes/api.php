<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'App\Http\Controllers\Api\V1'], function () {
    //Роуты на регистрацию, авторизацию и выход
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth'], function () {
        Route::post('register', 'RegisterController');
        Route::post('login', 'LoginController');
        Route::get('logout', 'LogoutController');
    });

    Route::group(['namespace' => 'Product', 'prefix' => 'products'], function () {
        Route::get('/', 'ProductController@getAllProducts');
        Route::get('/{slug}', 'ProductController@getSlugProduct');
    });

    Route::group(['namespace' => 'Cart', 'prefix' => 'cart'], function () {
        Route::get('/{cart_id}', 'CartController@show');
        Route::post('/add/{cart_id}/{product_slug}', 'CartController@add');
        Route::post('/reduce/{card_id}/{product_slug}', 'CartController@reduce');
        Route::delete('/destroy/{cart_id}/{product_slug}', 'CartController@destroy');
    });

    //Роуты для авторизированных пользователей
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/user', function (){
            return response()->json([
                dd(auth()->user())
            ]);
        });
        Route::group(['namespace' => 'Order', 'prefix' => 'orders'], function () {
            Route::get('/', 'OrderController@show');
            Route::post('/store/{cart_id}', 'OrderController@store');
        });
    });
});
