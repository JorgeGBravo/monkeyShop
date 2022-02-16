<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{


    public function register(Request $request)
    {
        $this->onlyAdmin();
        $validatedData = (new ValidateDataController)->registerValidateData($request);
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
        return response()->json($user);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only(strtolower('email'), 'password'))) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }
        $user = User::where('email', strtolower($request['email']))->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
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
                return response()->json(['message' => 'Updated Password']);
            }
        }
        return response()->json(['message' => 'You are not a registered user'], 403);
    }

    public function changeIsAdmin(Request $request)
    {
        $this->onlyAdmin();
        $validatedData = (new ValidateDataController())->changeIsAdminValidateData($request);
        if ($validatedData->fails()) {
            return response()->json(['message' => $validatedData->getMessageBag()->first()], 400);
        }

        $user = User::where('id', $request->input('id'))->where('name', strtolower($request->input('name')))->get();
        if (count($user) != 0) {
            ($user[0]->isAdmin === 0) ? $user[0]->isAdmin = 1 : $user[0]->isAdmin = 0;
            $user[0]->save();
            if ($user[0]->isAdmin === 1) {
                return response()->json(['message' => 'User is now Administrator']);
            }
            return response()->json(['message' => 'The user is no longer an administrator']);
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
