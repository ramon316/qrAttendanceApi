<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{

    /* User Register */
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'employee_id' => 'required|string|max:10|unique:users',
        ]);

        /* if validation fail */
        if ($validated->fails()) {
            return response()->json([
                'messsage' => 'Validation Error',
                'error' => $validated->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'employee_id' => $request->employee_id,
        ]);

        $token = $user->CreateToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /* User Login */
    public function login(Request $request)
    {
        $validated = Validator::make($request->all(),[
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        /* if validation fail */
        if ($validated->fails()) {
            return response()->json([
                'messsage' => 'Validation Error',
                'error' => $validated->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        /* if password not match or no exist user */
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'messsage' => 'Invalid Credentials',
            ], 401);
        }

        /* Eliminamos tokens anterioress */
        $user->tokens()->delete();
        /* Creamos nuevo token */
        $token = $user->createToken('auth_token', ['*'], now()->addHours(2))->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /* User Logout */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'messsage' => 'Logout Successfully',
        ], 200);
    }

    /* User Profile */
    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ], 200);
    }

    public function checkStatus(Request $request)
    {

        $user = $request->user();

        // Eliminar token actual
        $request->user()->currentAccessToken()->delete();

        // Crear nuevo token
        $newToken = $user->createToken('auth_token', ['*'], now()->addHours(2))->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $newToken,
        ], 200);
    }
}

