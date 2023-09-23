<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use App\Models\Chapters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChaptersController extends Controller
{
	public function index(Request $request)
	{
		$chapter = Chapters::query();

		$courseId = $request->query('course_id');

		// filter by course id
		$chapter->when($courseId, function ($query) use ($courseId) {
			return $query->where('course_id', '=', $courseId);
		});

		return response()->json([
			'status' => 'Success',
			'data' => $chapter->get()
		]);
	}

	public function show($id)
	{
		$chapter = Chapters::find($id);

		if (!$chapter) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Chapter not found'
			], 404);
		}

		return response()->json([
			'status' => 'Success',
			'data' => $chapter
		]);
	}

	public function create(Request $request)
	{
		$rules = [
			'name' => 'required|string',
			'course_id' => 'required|integer',
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

		$chapter = Chapters::create($data);

		return response()->json([
			'status' => 'Success',
			'data' => $chapter
		]);
	}

	public function update(Request $request, $id)
	{
		$rules = [
			'name' => 'string',
			'course_id' => 'integer',
		];

		$data = $request->all();

		$validator = Validator::make($data, $rules);

		if ($validator->fails()) {
			return response()->json([
				'status' => 'Error',
				'message' => $validator->errors()
			], 400);
		}

		$chapter = Chapters::find($id);

		if (!$chapter) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Chapter not found'
			], 404);
		}

		$courseId = $request->input('course_id');

		if ($courseId) {
			$course = Courses::find($courseId);

			if (!$course) {
				return response()->json([
					'status' => 'Error',
					'message' => 'Course not found'
				], 404);
			}
		}

		$chapter->fill($data);
		$chapter->save();

		return response()->json([
			'status' => 'Success',
			'data' => $chapter
		]);
	}

	public function destroy($id)
	{
		$chapter = Chapters::find($id);

		if (!$chapter) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Chapter not found'
			], 404);
		}

		$chapter->delete();

		return response()->json([
			'status' => 'Success',
			'message' => 'Chapter has been deleted'
		]);
	}
}
