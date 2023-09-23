<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewsController extends Controller
{
	public function create(Request $request)
	{
		$rules = [
			'user_id' => 'required|integer',
			'course_id' => 'required|integer',
			'rating' => 'required|integer|min:1|max:5',
			'note' => 'string'
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

		$isExistReview = Reviews::where('course_id', '=', $courseId)
			->where('user_id', '=', $userId)
			->exists();

		if ($isExistReview) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Review already exist'
			], 409);
		}

		$review = Reviews::create($data);

		return response()->json([
			'status' => 'Success',
			'data' => $review
		], 200);
	}

	public function update(Request $request, $id)
	{
		$rules = [
			'rating' => 'integer|min:1|max:5',
			'note' => 'string'
		];

		// get all datas from body except user id and course id
		$data = $request->except('user_id', 'course_id');

		$validator = Validator::make($data, $rules);

		if ($validator->fails()) {
			return response()->json([
				'status' => 'Error',
				'message' => $validator->errors()
			], 400);
		}

		// cek if review is exist in database
		$review = Reviews::find($id);

		if (!$review) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Review not found'
			], 404);
		}

		$review->fill($data);

		$review->save();

		return response()->json([
			'status' => 'Success',
			'data' => $review
		], 200);
	}

	public function destroy($id)
	{
		// cek if review is exist in database
		$review = Reviews::find($id);

		if (!$review) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Review not found'
			], 404);
		}

		$review->delete();

		return response()->json([
			'status' => 'Success',
			'message' => 'Review has been deleted'
		], 200);
	}
}
