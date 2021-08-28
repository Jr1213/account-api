<?php

namespace App\Http\Controllers;

use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // login with exsited account
    public function login(Request $req)
    {
        $validation = Validator::make($req->all(),
            [
                'password' => 'required|min:5',
                'email' => 'required|email',
            ]
        );
        if ($validation->fails()) {
            return \response()->json($validation->errors());
        }
        if ($user = User::where('email', '=', $req->email)->first() == null) {
            return \response()->json(['email' => ['invaild email']]);
        }
        $user = User::where('email', '=', $req->email)->first();
        if (Hash::check($req->password, $user->password)) {
            return \response()->json(['token' => $user->token]);
        }
        return \response()->json(['password' => ['invalid password']]);
    }
    // register new acount
    public function join(Request $req)
    {
        $validation = Validator::make($req->all(),
            [
                'name' => 'required|min:5',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:5',
            ]
        );
        if ($validation->fails()) {
            return \response()->json($validation->errors());
        }
        $user = new User;
        $user->name = $req->name;
        $user->email = $req->email;
        $user->password = Hash::make($req->password);
        $user->token = Str::random(64);
        $user->save();
        return \response()->json(
            [
                'token' => $user->token,
                'message' =>'registration complete',
            ]);
    }
    // getting user information
    public function user($userToken)
    {
        $user = User::where('token', '=', $userToken)->first();
        return \response()->json($user);
    }
}
