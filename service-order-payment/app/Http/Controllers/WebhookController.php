<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\PaymentLogs;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
	public function midtransHandler(Request $request)
	{
		$data = $request->all();

		$signatureKey = $data['signature_key'];

		$orderId = $data['order_id'];
		$statusCode = $data['status_code'];
		$grossAmount = $data['gross_amount'];
		$serverKey = env('MIDTRANS_SERVER_KEY');

		// generate signature key
		$mySignatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

		$transactionStatus = $data['transaction_status'];
		$type = $data['payment_type'];
		$fraudStatus = $data['fraud_status'];

		// signature key check
		if ($signatureKey !== $mySignatureKey) {
			return response()->json([
				'status' => 'error',
				'message' => 'Invalid signature key!'
			], 400);
		}

		// order id ex: 1-ak3su
		$realOrderId = explode('-', $orderId);
		$order = Orders::find($realOrderId[0]);

		// order id check
		if (!$order) {
			return response()->json([
				'status' => 'error',
				'message' => 'Order id not found'
			], 404);
		}

		if ($order->status === 'success') {
			return response()->json([
				'status' => 'error',
				'message' => 'Operation not permitted'
			], 405);
		}

		// transaction status check
		if ($transactionStatus == 'capture') {
			if ($fraudStatus == 'challenge') {
				// TODO set transaction status on your database to 'challenge'
				// and response with 200 OK
				$order->status = 'challenge';
			} else if ($fraudStatus == 'accept') {
				// TODO set transaction status on your database to 'success'
				// and response with 200 OK
				$order->status = 'success';
			}
		} else if ($transactionStatus == 'settlement') {
			// TODO set transaction status on your database to 'success'
			// and response with 200 OK
			$order->status = 'success';
		} else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
			// TODO set transaction status on your database to 'failure'
			// and response with 200 OK	
			$order->status = 'failure';
		} else if ($transactionStatus == 'pending') {
			// TODO set transaction status on your database to 'pending' / waiting payment
			// and response with 200 OK	
			$order->status = 'pending';
		}

		$dataLog = [
			'status' => $transactionStatus,
			'raw_response' => json_encode($data),
			'order_id' => $realOrderId[0],
			'payment_type' => $type,
		];

		// save the data log to Payment Log table
		PaymentLogs::create($dataLog);
		$order->save();

		// give access to the premium user if the payment is 'success'
		if ($order->status === 'success') {
			createPremiumAccess([
				'user_id' => $order->user_id,
				'course_id' => $order->course_id
			]);
		}

		return response()->json('ok');
	}
}
