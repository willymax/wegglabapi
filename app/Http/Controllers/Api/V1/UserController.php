<?php

namespace App\Http\Controllers\Api\V1;

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PaypalSubscription;
use App\Models\User;
use Illuminate\Http\Request;

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

    public function subscribeUser(Request $request)
    {
        $this->validate($request, [
            'subscription_id' => 'required',
            // 'status' => 'required',
            // 'start_time' => 'required',
        ]);

        /**
         * @var User $user
         */
        $user = auth()->user();
        $paypalSubscription = PaypalSubscription::updateOrCreate(
            ['user_id' => $user->id],
            ['subscription_id' => $request->subscription_id, 'status' => 'ACTIVE', 'start_time' => '']
        );
        $user = User::find($user->id);
        return $this->responseWithItem($user);
    }
}
