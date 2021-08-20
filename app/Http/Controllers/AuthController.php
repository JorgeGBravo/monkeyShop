<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $admin = DB::select('select isAdmin from users where id ="' . Auth::id() . '"');

        if ($admin[0]->isAdmin === 0) {
            return 'You do not have Administrator permissions';
        }

        $validatedData = $request->validate([                           // validate the data format
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([                                          // create a new user
            'name' => $validatedData['name'],
            'surname' => $validatedData['surname'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;      // generate a new token for user

        return response()->json([                                       // return a json with new Token
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(Request $request)                             // verification email and password match some user, in this case
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function changePassword(Request $request)
    {
        $validatedData = $request->validate([                           // validate the data format
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'newPassword' => 'required|string|min:8',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        DB::select('update users set password ="' . Hash::make($validatedData['newPassword']) . '"');

        return 'Updated Password';
    }



}
