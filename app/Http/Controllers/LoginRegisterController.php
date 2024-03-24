<?php

namespace App\Http\Controllers;

use App\Http\Requests\Login;
use App\Http\Requests\Register;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class LoginRegisterController extends Controller
{
    public function register(Register $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;

        $response = [
            'status' => 'success',
            'data' => $data,
        ];

        return response()->json($response, Response::HTTP_CREATED);
    }

    public function login(Login $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if(!Hash::check($request->password, $user->password)) {
            return response()->json(
                ['status' => 'failed'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;

        $response = [
            'status' => 'success',
            'data' => $data,
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json(
            ['status' => 'success'],
            Response::HTTP_OK
        );
    }
}
