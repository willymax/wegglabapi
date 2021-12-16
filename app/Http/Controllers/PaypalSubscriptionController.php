<?php

namespace App\Http\Controllers;

use App\Models\PaypalSubscription;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\User;

class PaypalSubscriptionController extends Controller
{
    //
    public function index(Request $request)
    {
        $paginator = PaypalSubscription::with('user')->paginate($request->perPage);
        return $this->respondWithPagination($paginator, $paginator->items());
    }
    public function show(Request $request, $id)
    {
        $subscription = PaypalSubscription::with('plan')->findOrFail($id);
        return $this->responseWithItem($subscription);
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

    public function unsubscribeUser(Request $request)
    {
        $this->validate($request, [
            'subscription_id' => 'required|exists:paypal_subscriptions,subscription_id',
        ]);
        $subscription = PaypalSubscription::where('subscription_id', $request->subscription_id)->first();

        $subscription->status = 'CANCELLED';
        $subscription->save();

        return $this->itemDeletedResponse($subscription);
    }
}
