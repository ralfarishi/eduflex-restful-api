<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use App\Models\MyCourses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MyCoursesController extends Controller
{
	public function index(Request $request)
	{
		$myCourses = MyCourses::query()->with('course');

		$userId = $request->query('user_id');

		$myCourses->when($userId, function ($query) use ($userId) {
			return $query->where('user_id', '=', $userId);
		});

		return response()->json([
			'status' => 'Success',
			'data' => $myCourses->get()
		], 200);
	}

	public function create(Request $request)
	{
		$rules = [
			'course_id' => 'required|integer',
			'user_id' => 'required|integer',
		];

		$data = $request->all();

		$validator = Validator::make($data, $rules);

		if ($validator->fails()) {
			return response()->json([
				'status' => 'Error',
				'message' => $validator->errors()
			], 400);
		}

		// course_id check in database if exist
		$courseId = $request->input('course_id');
		$course = Courses::find($courseId);

		if (!$course) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Course not found'
			], 404);
		}

		// check user_id in service user
		$userId = $request->input('user_id');
		$user = getUser($userId);

		if ($user['status'] === 'Error') {
			return response()->json([
				'status' => $user['status'],
				'message' => $user['message']
			], $user['http_code']);
		}

		// check that there is no duplication of data
		$isExistMyCourse = MyCourses::where('course_id', '=', $courseId)
			->where('user_id', '=', $userId)
			->exists();

		if ($isExistMyCourse) {
			return response()->json([
				'status' => 'Error',
				'message' => 'User already take this course'
			], 409);
		}

		if ($course->type === 'premium') {
			if ($course->price === 0) {
				return response()->json([
					'status' => 'error',
					'message' => 'Price cannot be 0'
				], 405);
			}

			$order = postOrder([
				'user' => $user['data'],
				'course' => $course->toArray()
			]);

			// echo "<pre>" . print_r($order, 1) . "</pre>";

			if ($order['status'] === 'error') {
				return response()->json([
					'status' => $order['status'],
					'message' => $order['message']
				], $order['http_code']);
			}

			return response()->json([
				'status' => $order['status'],
				'data' => $order['data']
			]);
		} else {
			$myCourse = MyCourses::create($data);

			return response()->json([
				'status' => 'Success',
				'data' => $myCourse
			], 200);
		}
	}

	public function createPremiumAccess(Request $request)
	{
		$data = $request->all();
		$myCourse = MyCourses::create($data);

		return response()->json([
			'status' => 'success',
			'data' => $myCourse
		]);
	}
}
