<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentApprovalController extends Controller
{
    public function approvePaymentv1(Request $request)
    {
        //Get the necessary information from the request
        // $jobId = $request->input('job_id');
        // $freelancerPayPalEmail = $request->input('freelancer_paypal_email');
        // $amountToRelease = $request->input('amount_to_release');

        $jobId = 5;
        $freelancerPayPalEmail = 'sb-qcrrc26086329@personal.example.com';
        $amountToRelease = 100;

        //Initialize the PayPal client.
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));

        // Create a payment to the freelancer
            // $response = $provider->createPayout([
            //     'sender_batch_header' => [
            //         'sender_batch_id' => uniqid(),
            //         'email_subject' => 'Payment for completed job',                
            //     ],
            //     'items' => [
            //         [
            //             'recipient_type' => 'EMAIL',
            //             'receiver' => $freelancerPayPalEmail,
            //             'amount' => [
            //                 'value' => $amountToRelease,
            //                 'currency' => 'USD', //Adjust the currency as needed. 
            //             ],
            //             'note' => 'Payment for job ID: '. $jobId,
            //         ],
            //     ],
            // ]);
        //  $response = $provider->makePay(
        //     $freelancerPayPalEmail,
        //     $amountToRelease,
        //     ['note' => 'Payment for job ID: ' . $jobId]
        // );

        //Check the response and handle success or error.
        if ($response['status'] === 'success') {
            // Funds have been successfully transferred to freelancer. 
            // Update database or escrow records to mark the funds as released. 

            //Return json response indicating success

            return response()->json([
                'Payment approved and funds released'
            ], 200);
        } else {
            // Handle the error, log it, and return a json response indicating the error
            return response()->json(['error' => "Payment approval and funds release failed"], 400);
        }
    }

    public function approvePayment(Request $request)
    {
        //Get the necessary information from the request
        // $jobId = $request->input('job_id');
        // $freelancerPayPalEmail = $request->input('freelancer_paypal_email');
        // $amountToRelease = $request->input('amount_to_release');

        $jobId = 5;
        $freelancerPayPalEmail = 'sb-qcrrc26086329@personal.example.com';
        $amountToRelease = 100;

        //Initialize the PayPal client.
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        
        $data = json_decode('{
            "sender_batch_header": {
                "sender_batch_id": "Payouts_2018_100g0075",
                "email_subject": "You have a payout!",
                "email_message": "You have received a payout! Thanks for using our service!"
            },
            "items": [
                {
                    "recipient_type": "EMAIL",
                    "amount": {
                        "value": 9.87,
                        "currency": "USD"
                    },
                    "note": "Thanks for your patronage!",
                    "sender_item_id": "201403140001",
                    "receiver": "sb-qcrrc26086329b@personal.example.com",
                    "alternate_notification_method": {
                        "phone": {
                        "country_code": "91",
                        "national_number": "9999988888"
                        }
                    },
                    "notification_language": "fr-FR"
                }                
            ]
        }', true);

        // return config('paypal');

        $response = $provider->createBatchPayout($data);
        // $payoutBatchId = "PQKC23V4RSZLJ"; // Replace with your batch ID

        // $response = $provider->showBatchPayoutDetails($payoutBatchId);
        return $response;
        //Check the response and handle success or error.
        if ($response['status'] === 'success') {
            // Funds have been successfully transferred to freelancer. 
            // Update database or escrow records to mark the funds as released. 

            //Return json response indicating success

            return response()->json([
                'Payment approved and funds released'
            ], 200);
        } else {
            // Handle the error, log it, and return a json response indicating the error
            return response()->json(['error' => "Payment approval and funds release failed"], 400);
        }

    }
}
