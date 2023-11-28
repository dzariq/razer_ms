<?php
namespace App\Http\Middleware;

use App\merchant_payment_channel;
use Illuminate\Support\Facades\Crypt;
use Closure;

class CheckMerchant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $encrypted_data = $request->route('encrypted_data');

        // #req data
        // $requestData = array(
        //     'name' => $request->get('name'),
        //     'email' => $request->get('email'),
        //     'contact' => $request->get('contact'),
        //     'desc' => $request->get('desc'),
        //     'amount' => $request->get('amount'),
        //     'currency' => $request->get('currency'),
        //     'refNo' => $request->get('refNo'),
        //     'channel' => $request->get('channel'),
        //     'merchant_id' => $request->get('merchantId')
        // );
        // $encrypted_data = Crypt::encrypt($requestData);
        // print_r($encrypted_data);
        // die;
        $proceed = true;

        // Decrypt the merchant ID
        $requestData = Crypt::decrypt($encrypted_data);

        #check DB payment channel is enabled
        $merchantPaymentChannel = merchant_payment_channel::
            where('merchant_id', $requestData['merchant_id'])
            ->where('channel_name', $requestData['channel'])->first();

        if (!$merchantPaymentChannel) {
            return response('Unauthorized action.', 403);
            exit();
        }
        if ($proceed) {
            $request->merge([
                'merchantId' => env('RAZER_MERCHANT_ID'),
                'verifyKey' => env('RAZER_VERIFY_KEY')
            ]);
            $request->merge([
                'refNo' => $requestData['refNo'],
                'name' => $requestData['name'],
                'email' => $requestData['email'],
                'contact' => $requestData['contact'],
                'desc' => $requestData['desc'],
                'amount' => $requestData['amount'],
                'currency' => $requestData['currency'],
                'paymentChannel' => strtoupper($requestData['channel']),
            ]);

            return $next($request);
        }

        // Unauthorized response
        return response('Unauthorized action.', 403);
    }
}