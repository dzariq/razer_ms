<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CallbackController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $encryptedData = '';

        $callbackData = array(
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'contact' => $request->get('contact'),
            'desc' => $request->get('desc'),
            'amount' => $request->get('amount'),
            'currency' => $request->get('currency'),
            'refNo' => $request->get('refNo'),
            'channel' => $request->get('channel'),
            'merchant_id' => $request->get('merchantId')
        );
        $encrypted_data = Crypt::encrypt($callbackData);
        print_r($encrypted_data);
        die;

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
