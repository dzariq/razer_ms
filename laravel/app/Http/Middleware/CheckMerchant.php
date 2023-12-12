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

        $proceed = true;

        // Decrypt the merchant ID
        $requestData = Crypt::decrypt($encrypted_data);

        #check DB payment channel is enabled
        $merchantPaymentChannel = merchant_payment_channel::
            where('channel_name', $requestData['channel'])->first();

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