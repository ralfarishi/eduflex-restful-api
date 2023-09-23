<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
	public function index(Request $request)
	{
		$userId = $request->input('user_id');
		$orders = Orders::query();

		// filter based on user id
		$orders->when($userId, function ($query) use ($userId) {
			return $query->where('user_id', "=", $userId);
		});

		return response()->json([
			'status' => 'success',
			'data' => $orders->get()
		]);
	}

	public function create(Request $request)
	{
		// req body
		$user = $request->input('user');
		$course = $request->input('course');

		$order = Orders::create([
			'user_id'     => $user['id'],
			'course_id' => $course['id']
		]);

		// midtrans detail
		$transactionDetails = [
			'order_id' => $order->id . '-' . Str::random(5),
			'gross_amount' => $course['price']
		];

		$itemDetails = [
			[
				'id' => $course['id'],
				'price' => $course['price'],
				'quantity' => 1,
				'name' => $course['name'],
				'brand' => 'EduFlex',
				'category' => 'Online Course'
			]
		];

		$customerDetails = [
			'first_name' => $user['name'],
			'email' => $user['email'],
		];

		// midtrans parameter
		$midtransParams = [
			'transaction_details' => $transactionDetails,
			'item_details'                 => $itemDetails,
			'customer_details'         => $customerDetails
		];

		$midtransSnapUrl = $this->getMidtransSnapUrl($midtransParams);

		// generate snap url
		$order->snap_url = $midtransSnapUrl;

		$order->metadata = [
			'course_id' => $course['id'],
			'course_price' => $course['price'],
			'course_name' => $course['name'],
			'thumbnail' => $course['thumbnail'],
			'course_level' => $course['level'],
		];

		// save data to database
		$order->save();

		return response()->json([
			'status' => 'success',
			'data' => $order
		]);
	}

	private function getMidtransSnapUrl($params)
	{
		\Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
		\Midtrans\Config::$isProduction = (bool) env('MIDTRANS_PRODUCTION');
		\Midtrans\Config::$isSanitized = (bool) env('MIDTRANS_SANITIZED');
		\Midtrans\Config::$is3ds = (bool) env('MIDTRANS_3DS');

		$snapUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

		return $snapUrl;
	}
}
