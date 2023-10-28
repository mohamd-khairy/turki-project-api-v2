<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function getCustomers(Request $request)
    {
        return response()->json(['success' => true, 'message' => '', 'description' => "", "code" => "200",
            "data" => Customer::all('mobile', 'name', 'wallet', 'loyalty_points')], 200);
    }
}
