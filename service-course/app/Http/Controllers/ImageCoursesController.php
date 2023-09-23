<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use App\Models\ImageCourses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageCoursesController extends Controller
{
	public function create(Request $request)
	{
		$rules = [
			'course_id' => 'required|integer',
			'image' => 'required|url',
		];

		$data = $request->all();

		$validator = Validator::make($data, $rules);

		if ($validator->fails()) {
			return response()->json([
				'status' => 'Error',
				'message' => $validator->errors()
			], 400);
		}

		$courseId = $request->input('course_id');
		$course = Courses::find($courseId);

		if (!$course) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Course not found'
			], 404);
		}

		$imageCourse = ImageCourses::create($data);

		return response()->json([
			'status' => 'Success',
			'data' => $imageCourse
		]);
	}

	public function destroy($id)
	{
		$imageCourse = ImageCourses::find($id);

		if (!$imageCourse) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Image not found'
			], 404);
		}

		$imageCourse->delete();

		return response()->json([
			'status' => 'Success',
			'message' => 'Image has been deleted'
		]);
	}
}
