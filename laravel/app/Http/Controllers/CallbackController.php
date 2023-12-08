<?php

namespace App\Http\Controllers;

use App\payment_channel_response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Log;

class CallbackController extends Controller
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

        Log::info(json_encode($request->all()));

        #insert into table response
        $payment_channel_response = new payment_channel_response();
        $payment_channel_response->skey = $skey;
        $payment_channel_response->transaction_reference = $orderid;
        $payment_channel_response->tranID = $tranID;
        $payment_channel_response->domain = $domain;
        $payment_channel_response->status = $status;
        $payment_channel_response->amount = $amount;
        $payment_channel_response->currency = $currency;
        $payment_channel_response->paydate = $paydate;
        $payment_channel_response->orderid = $orderid;
        $payment_channel_response->appcode = $appcode;
        $payment_channel_response->error_code = $error_code;
        $payment_channel_response->error_desc = $error_desc;
        $payment_channel_response->channel = $channel;
        $payment_channel_response->save();

        $encryptedData = '';

        #skip pending status
        if($status == '22')
        {
            exit();
        }

        if ($status == '00')
            $senangpay_status = 'paid';
        else if ($status == '11')
            $senangpay_status = 'failed';

        $callbackData = array(
            'transaction_reference' => $orderid,
            'status' => $senangpay_status,
        );

        $encrypted_data = Crypt::encrypt($callbackData);

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => env('SENANGPAY_PAYMENT_CALLBACK_URL'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'data' => $encryptedData
                ),

            )
        );

        $response = json_decode(curl_exec($curl));

        curl_close($curl);


    }
}
