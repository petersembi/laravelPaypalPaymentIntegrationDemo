<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class PaymentController extends Controller
{
    public function handlePayment(Request $request)
    {
        $paymentAmount = 500;

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));      
        $paypalToken = $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('success.payment'),
                "cancel_url" => route('cancel.payment'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" =>  $paymentAmount,
                    ]
                ]
            ]

        ]);

        if (isset($response['id']) && $response['id'] != null){
            foreach($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);                    
                }
            }
            return redirect()
                    ->route('cancel.payment')
                    ->with('error', 'Something went wrong');
        } else {
            return redirect()
                ->route('create.payment')
                ->with('error', $response['message'] ?? "Something went wrong");       
        }

    }

    public function paymentCancel()
    {
        return redirect()
            ->route('create.payment')
            ->with('error', $response['message'] ?? 'You have cancelled the transaction.');

    }

    public function paymentSuccess(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
        if (isset($response['status']) && $response['status'] == 'COMPLETED'){
           
            $userId = 2;
            $jobId = 40;
            //Payment was successful, so save payment details in the db
            
            $payment['user_id'] = $userId;
            $payment['job_id'] = $jobId;
            $payment['transaction_id'] = $response['id'];
            $payment['amount'] = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $payment['currency'] = $response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];
            $payment['net_amount_from_client'] = $response['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown']['net_amount']['value'];
            $payment['payment_status'] = $response['status'];
            $payment['payment_timestamp'] = now();
            $payment['transaction_type'] = 'deposit';

            return $payment;
            
            return redirect()
                ->route('create.payment')
                // ->with('success', 'Transaction complete.');
                ->with('success', $response);
        } else {
            return redirect()
                ->route('create.payment')
                ->with('error', $response['message'] ?? 'Something went wrong');
        }

    }
}
