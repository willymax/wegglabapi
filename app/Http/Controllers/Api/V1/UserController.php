<?php

namespace App\Http\Controllers\Api\V1;

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PaypalSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $paginator = User::paginate($request->perPage);
        return $this->respondWithPagination($paginator, $paginator->items());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = User::find($id);
        return $this->responseWithItem($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $user = User::findOrFail($id);
        $this->validate($request, [
            'email' => 'unique:users,email,' . $user->id
        ]);
        if (isset($request->first_name)) {
            $user->first_name = $request->first_name;
        }
        if (isset($request->last_name)) {
            $user->last_name = $request->last_name;
        }
        if (isset($request->email)) {
            $user->email = $request->email;
        }
        $user = $user->save();
        $user = User::findOrFail($id);
        return $this->responseWithItem($user);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request, $id)
    {
        //
        $user = User::findOrFail($id);
        $rules = [
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required',
        ];
        $this->validate($request, $rules);

        $old_password = $request->old_password;
        $new_password = $request->new_password;
        //$new_password_confirmation = $request->new_password_confirmation;

        if (Auth::check()) {
            /**
             * @var User $logged_user
             */
            $logged_user = Auth::user();

            if (Hash::check($old_password, $logged_user->password)) {
                $logged_user->password = Hash::make($new_password);
                $logged_user->save();
                return $this->respond(['code' => 0]);
            }
            return $this->respondWithError(trans('app.wrong_old_password'));
        }
        return $this->respondWithError(trans('app.unauthorized_access'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function createOrGetStripeCustomer()
    {
        /**
         * @var User $user
         */
        $user = auth()->user();
        $stripeCustomer = $user->createOrGetStripeCustomer();
        return $this->responseWithItem($stripeCustomer);
    }

    public function getUserBalance(Request $request)
    {
        /**
         * @var User $user
         */
        $user = auth()->user();
        $balance = $user->balance();
        return $this->responseWithItem(array('balance' => $balance));
    }

    /**
     * Creates an intent for payment so we can capture the payment
     * method for the user.
     *
     * @param Request $request The request data from the user.
     */
    public function getSetupIntent(Request $request)
    {
        return $this->responseWithItem($request->user()->createSetupIntent());;
    }

    public function updateAvatar(Request $request)
    {
        /**
         * @var User
         */
        $user = auth()->user();
        $this->validate($request, [
            'new_avatar' => 'required|mimes:jpeg,png,jpg',
        ]);
        $deleted = Storage::delete($user->avatar);
        $file = $request->file('new_avatar');
        $name = $user->id . '_' . md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        $path = Storage::disk('public')->putFileAs('avatars', $file, $name);
        $user->avatar = $path;
        $user->save();
        return $this->itemUpdatedResponse($user, 'Cover photo updated');
    }

    public function subscribeUser(Request $request)
    {
        $this->validate($request, [
            'subscription_id' => 'required',
            'paypal_plan_id' => 'required',
            // 'status' => 'required',
            // 'start_time' => 'required',
        ]);

        /**
         * @var User $user
         */
        $user = auth()->user();
        $paypalSubscription = PaypalSubscription::updateOrCreate(
            ['user_id' => $user->id],
            ['subscription_id' => $request->subscription_id, 'paypal_plan_id' => $request->paypal_plan_id, 'status' => 'ACTIVE', 'start_time' => '']
        );
        $user = User::find($user->id);
        return $this->responseWithItem($user);
    }
}
