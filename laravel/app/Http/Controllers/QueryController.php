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

        Log::info('QUERY FOR TXN ID: '.$orderId.'|'.$rawData);

        $dataResponse = json_decode($rawData);

        curl_close($curl);

        return json_encode($dataResponse);




    }
}
