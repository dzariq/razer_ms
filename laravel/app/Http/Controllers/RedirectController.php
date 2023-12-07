<?php

namespace App\Http\Controllers;

use App\payment_channel_response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class RedirectController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $skey = $request->get('sKey');
        $tranID = $request->get('tranID');
        $domain = $request->get('domain');
        $status = $request->get('status');
        $amount = $request->get('amount');
        $currency = $request->get('currency');
        $paydate = $request->get('paydate');
        $orderid = $request->get('orderid');
        $appcode = $request->get('appcode');
        $error_code = $request->get('error_code');
        $error_desc = $request->get('error_desc');
        $channel = $request->get('channel');

        if ($status == '22') {
            return view('payment_pending');
        } else {
            if ($status == '00')
                $senangpay_status = 'paid';
            else if ($status == '11')
                $senangpay_status = 'failed';
            return redirect()->to(env("SENANGPAY_PAYMENT_REDIRECT_URL") . '/' . $orderid . '/' . $senangpay_status);
        }
    }
}
