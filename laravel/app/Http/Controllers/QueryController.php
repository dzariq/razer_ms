<?php

namespace App\Http\Controllers;

use App\payment_channel_response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Log;

class QueryController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $orderId = $request->get('transaction_reference');
        $amount = $request->get('amount');
        $domain = env("RAZER_MERCHANT_ID");

        //MD5 encryption of STR
        $skey = md5($orderId . $domain . env("RAZER_VERIFY_KEY") . $amount);

        $requestData = array(
            "oID" => $orderId,
            "skey" => $skey,
            "domain" => $domain,
            "amount" => $amount,
            "type" => 2
        );

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.merchant.razer.com/RMS/query/q_by_oid.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $requestData
            )
        );

        $rawData = curl_exec($curl);

        Log::info('QUERY FOR TXN ID: ' . $orderId . '|' . $rawData);

        $dataResponse = json_decode($rawData);

        #insert into table response
        $payment_channel_response = new payment_channel_response();
        $payment_channel_response->skey = '';
        $payment_channel_response->transaction_reference = $dataResponse->OrderID;
        $payment_channel_response->tranID = $dataResponse->TranID;
        $payment_channel_response->domain = $dataResponse->Domain;
        $payment_channel_response->status = $dataResponse->StatCode;
        $payment_channel_response->amount = $dataResponse->Amount;
        $payment_channel_response->currency = $dataResponse->Currency;
        $payment_channel_response->paydate = $dataResponse->BillingDate;
        $payment_channel_response->orderid = $dataResponse->OrderID;
        $payment_channel_response->appcode = '';
        $payment_channel_response->error_code = $dataResponse->ErrorCode;
        $payment_channel_response->error_desc = $dataResponse->ErrorDesc;
        $payment_channel_response->channel = $dataResponse->Channel;
        $payment_channel_response->save();

        curl_close($curl);

        #send callback to senangpay
        if ($dataResponse->StatCode == '00')
            $senangpay_status = 'paid';
        else if ($dataResponse->StatCode == '11')
            $senangpay_status = 'failed';

        $callbackData = array(
            'transaction_reference' => $dataResponse->OrderID,
            'status' => $senangpay_status,
        );

        $encryptedData = Crypt::encrypt($callbackData);

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
        #end curl senangpay callback

        return json_encode($dataResponse);




    }
}
