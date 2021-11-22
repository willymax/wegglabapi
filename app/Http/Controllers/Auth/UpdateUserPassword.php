<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword extends Controller
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update(Request $request)
    {
        $input = $request->all();
        $user = auth()->user();
        $validator  = Validator::make($input, [
            'current_password' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ])->after(function ($validator) use ($user, $input) {
            if (!isset($input['current_password']) || !Hash::check($input['current_password'], $user->password)) {
                $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
            }
        });
        if ($validator->fails()) {
            $err = array();
            $errors = $validator->errors();
            foreach ($errors->get('current_password') as $message) {
                //
                array_push($err, (object) array('source' => 'current_password', 'detail' => $message));
            }
            foreach ($errors->get('password') as $message) {
                //
                array_push($err, (object) array('source' => 'password', 'detail' => $message));
            }
            return $this->respondValidationError($err);
        }

        // if ($validator->fails()) {
        //     if (!isset($input['current_password']) || !Hash::check($input['current_password'], $user->password)) {
        //         $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
        //     }
        //     // return $this->respondValidationError($validator->errors()->toArray());
        // }

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();

        $user =
            auth()->user();

        return response()->json($user, 201);
    }
}
