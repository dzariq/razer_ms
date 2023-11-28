<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RazerController extends Controller
{
    public function __construct()
    {
    }

    public function payment(Request $request, $hash = '')
    {
        $merchantId = 'senangpay_Dev ';
        $verifyKey = $request->input('verifyKey');
        $refNo = $request->input('refNo');
        $paymentChannel = $request->input('paymentChannel');
        $currency = $request->input('currency');
        $amount = $request->input('amount');
        $name = $request->input('name');
        $email = $request->input('email');
        $contact = $request->input('contact');
        $desc = $request->input('desc');

        $hash_str = $amount.$merchantId.$refNo.$verifyKey;
        //MD5 encryption of STR
        $strmd5 = md5($hash_str);

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://pay.merchant.razer.com/RMS/API/Direct/1.4.0/index.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_SSL_VERIFYHOST  => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'MerchantID' => $merchantId,
                    'ReferenceNo' => $refNo,
                    'TxnType' => 'SALS',
                    'TxnChannel' => $paymentChannel,
                    'TxnCurrency' => $currency,
                    'TxnAmount' => $amount,
                    'CustName' => $name,
                    'Custemail' => $email,
                    'CustContact' => $contact,
                    'CustDesc' => $desc,
                    'Signature' => $strmd5
                ),

            )
        );

        $response = json_decode(curl_exec($curl));

        curl_close($curl);

        return view('payment_redirect', ['data' => $response->TxnData]);

    }
}
