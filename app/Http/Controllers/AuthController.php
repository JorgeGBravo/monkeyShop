<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{


    public function register(Request $request)
    {
        $this->onlyAdmin();
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'isAdmin' => 'nullable|bool',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['message' => $validatedData->getMessageBag()->first()], 400);
        }

        $user = User::create([
            'name' => strtolower($request->input('name')),
            'surname' => strtolower($request->input('surname')),
            'email' => strtolower($request->input('email')),
            'password' => Hash::make($request->input('password')),
            'isAdmin' => $request->input('isAdmin'),
        ]);
        return response()->json($user, 200);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only(strtolower('email'), 'password'))) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        $user = User::where('email', strtolower($request['email']))->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['access_token' => $token, 'token_type' => 'Bearer'], 200);
    }

    public function changePassword(Request $request)
    {
        $user = User::where('email', '=', strtolower($request->input('email')))
            ->where('id', '=', Auth::id())
            ->get();
        if (count($user) != 0) {
            if ($user[0]->email === strtolower($request->input('email')))
            {
                $user[0]->password = Hash::make($request->input('newPassword'));
                $user[0]->save();
                return response()->json(['message' => 'Updated Password'], 200);
            }
        }
        return response()->json(['message' => 'You are not a registered user'], 403);
    }

    public function changeIsAdmin(Request $request)
    {
        $this->onlyAdmin();
        $validatedData = Validator::make($request->all(), [
            'id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['message' => $validatedData->getMessageBag()->first()], 400);
        }

        $user = User::where('id', $request->input('id'))->where('name', strtolower($request->input('name')))->get();
        if (count($user) != 0) {
            ($user[0]->isAdmin === 0) ? $user[0]->isAdmin = 1 : $user[0]->isAdmin = 0;
            $user[0]->save();
            if ($user[0]->isAdmin === 1) {
                return response()->json(['message' => 'User is now Administrator'], 200);
            }

            return response()->json(['message' => 'The user is no longer an administrator'], 200);
        }

        return response()->json(['message' => 'There is no user with these credentials'], 400);
    }

    public function onlyAdmin()
    {
        if (auth()->user()->isAdmin === 0) {
            return response()->json(['message' => 'You do not have Administrator permissions'], 403);
        }
    }

}
