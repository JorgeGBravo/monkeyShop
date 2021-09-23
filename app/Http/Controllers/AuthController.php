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
        $this->isAnAdmin();
        $validatedData = Validator::make($request->all() ,[
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'isAdmin' => 'nullable|bool',
        ]);
        $this->controllerValidateData($validatedData);
        $name = $request->input('name');
        $surname = $request->input('surname');
        $email = $request->input('email');
        $password = $request->input('password');
        $isAdmin = $request->input('isAdmin');

        $user = User::create([                                          // create a new user
            'name' => $name,
            'surname' => $surname,
            'email' => strtolower($email),
            'password' => Hash::make($password),
            'isAdmin' => $isAdmin,
        ]);
        return response()->json($user, 200);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only(strtolower('email'), 'password'))) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['access_token' => $token, 'token_type' => 'Bearer'], 200);
    }

    public function changePassword(Request $request)
    {
        $email = strtolower($request->input('email'));
        $user = User::get()
            ->where('email', '=', $email)
            ->where('id', '=', Auth::id());

        if (count($user) != 0) {
            if ($user[$this->arrayPositionAuth()]->email === $email) {
                DB::table('users')
                    ->where('id', Auth::id())
                    ->update(['password' => Hash::make($request->input('newPassword'))]);
                return response()->json(['message' => 'Updated Password'], 200);
            }
        }
        return response()->json(['message' => 'You are not a registered user, your token is not in the system.'], 403);
    }

    public function changeIsAdmin(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
        ]);
        $this->controllerValidateData($validatedData);
        $id = $request->input('id');
        $name = $request->input('name');
        $isAdmin = User::select('isAdmin')->where('name', $name)->get();
        $this->isAnAdmin();

        if ($isAdmin[0]->isAdmin === 0) {
            User::where('id', $id )->where('name', $name)->update(['isAdmin' => 1]);
            return response()->json(['message' => 'User is now Administrator'], 200);
        }
        User::where('id', $id )->where('name', $name)->update(['isAdmin' => 0]);
        return response()->json(['message' => 'The user is no longer an administrator'], 200);
    }

}
