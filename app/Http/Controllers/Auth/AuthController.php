<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    use ApiResponser;
    public function register(Request $request)
    {
        $attr = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::create([
            'name' => $attr['name'],
            'password' => bcrypt($attr['password']),
            'email' => $attr['email']
        ]);

        return $this->success([
            'token' => $user->createToken('API Token')->plainTextToken
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            $err = array();
            $errors = $validator->errors();
            foreach ($errors->get('email') as $message) {
                //
                array_push($err, (object) array('source' => 'email', 'detail' => $message));
            }
            foreach ($errors->get('password') as $message) {
                //
                array_push($err, (object) array('source' => 'password', 'detail' => $message));
            }
            return $this->respondValidationError($err);
        }
        $attr = $request->all();

        if (!Auth::attempt($attr)) {
            return $this->errors('An error occurred', array((object) array('source' => 'password', 'detail' => 'Credentials not match')), 401);
        }

        return $this->success([
            'token' => auth()->user()->createToken('API Token')->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return [
            'message' => 'Token Revoked'
        ];
    }

    public function logoutEveryWhere()
    {
        // Revoke all tokens...
        auth()->user()->tokens()->delete();
        return $this->respondWithMessage('Tokens Revoked');
    }

    public function showResetForm(Request $request)
    {
        # code...
    }
}