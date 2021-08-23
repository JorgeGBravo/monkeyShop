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

        if (auth()->user()->isAdmin === 0) {

            return 'You do not have Administrator permissions';
        }

        $validatedData = $request->validate([                           // validate the data format
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'isAdmin' => 'nullable|bool',
        ]);

        $user = User::create([                                          // create a new user
            'name' => $validatedData['name'],
            'surname' => $validatedData['surname'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'isAdmin' => $validatedData['isAdmin'],
        ]);
        return $user;

    }

    public function login(Request $request)
    {
        log::info($request);
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

        $email = $request->input('email');

        $user = DB::select('select * from users where email="' . $email . '"and id="' .Auth::id(). '"');


        if ($user[0]->email === $request->input('email')) {
            DB::select('update users set password ="' . Hash::make($request->input('newPassword')) . '"where id="' . Auth::id() . '"');

            return 'Updated Password';
        }

        return 'You are not a registered user, your token is not in the system.';

    }

    public function changeIsAdmin(Request $request)
    {
        $validatedData = $request->validate([                           // validate the data format
            'id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
        ]);

        log::info(auth()->user()->isAdmin);
        log::info($validatedData['id']);
        log::info($validatedData['name']);

        $isAdmin = DB::select('select isAdmin from users where "' . $validatedData['id'] . '"and name = "' . $validatedData['name'] . '"');


        if (auth()->user()->isAdmin === 1) {
            if ($isAdmin[0]->isAdmin === 0) {

                DB::select('update users set isAdmin = 1 where "' . $validatedData['id'] . '"and name = "' . $validatedData['name'] . '"');
                return 'User is now Administrator';
            }

            DB::select('update users set isAdmin = 0 where "' . $validatedData['id'] . '"and name = "' . $validatedData['name'] . '"');
            return 'The user is no longer an administrator';

        }
        return 'Only administrators can make that query';
    }

}
