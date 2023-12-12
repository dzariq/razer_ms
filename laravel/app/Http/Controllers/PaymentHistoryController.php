<?php

namespace App\Http\Controllers;

use App\payment_channel_response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Log;

class PaymentHistoryController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $orderId = $request->get('transaction_reference');
        $domain = env("RAZER_MERCHANT_ID");

        $histories = payment_channel_response::where("transaction_reference", $orderId)->
        orderby('id','DESC')->get();

        return json_encode($histories);




    }
}
