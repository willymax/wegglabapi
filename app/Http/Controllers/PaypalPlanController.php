<?php

namespace App\Http\Controllers;

use App\Models\PaypalPlan;
use Illuminate\Http\Request;

class PaypalPlanController extends Controller
{
    //
    public function index()
    {
        $plans = PaypalPlan::all();
        return $this->collectionResponse($plans);
    }

    public function store(Request $request)
    {
        $plan = PaypalPlan::create($request->all());
        return $this->itemCreatedResponse($plan);
    }
}
