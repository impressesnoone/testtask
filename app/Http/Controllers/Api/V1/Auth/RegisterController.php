<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
                [
                    'name' => 'required|string',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|min:8',
                    'password_c' => 'required|same:password'
                ],
                [
                    'email.email' => 'Введите корректный email',
                    'email.required' => 'Email обязателен для заполнения',
                    'email.unique' => 'Введенный email уже зарегистрирован',
                    'password.required' => 'Пароль обязателен для заполнения',
                    'password.min' => 'Пароль должен состоять минимум из 8 символов',
                    'password_c.required' => 'Повторный пароль обязателен для заполения',
                    'password_c.same' => 'Пароли не совпадают',
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
            $user = User::create([
                'phone' => $request->phone,
                'email' => $request->email,
                'name' => $request->name,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'data' => [
                    'status' => 'true',
                    'code' => 201,
                    'message' => 'Регистрация прошла успешно!',
                    'userId' => $user->id,
                ]
            ], 201);

        } catch (\Throwable $tr) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Server Error',
                    'errors' => $tr->getMessage(),
                ]
            ], 500);
        }
    }
}
