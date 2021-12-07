<?php

namespace App\Http\Controllers;

use App\Models\PaypalSubscription;
use App\Models\Subscription;
use Illuminate\Http\Request;

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
}
