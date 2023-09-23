<?php

namespace App\Http\Controllers;

use App\Models\Lessons;
use App\Models\Chapters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonsController extends Controller
{
	public function index(Request $request)
	{
		$lesson = Lessons::query();

		$chapterId = $request->query('chapter_id');

		// filter by chapter id
		$lesson->when($chapterId, function ($query) use ($chapterId) {
			return $query->where('chapter_id', "=", $chapterId);
		});

		return response()->json([
			'status' => 'Success',
			'data' => $lesson->get()
		]);
	}

	public function show($id)
	{
		$lesson = Lessons::find($id);

		if (!$lesson) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Lesson not found'
			], 404);
		}

		return response()->json([
			'status' => 'Success',
			'data' => $lesson
		]);
	}

	public function create(Request $request)
	{
		$rules = [
			'name' => 'required|string',
			'video' => 'required|string',
			'chapter_id' => 'required|integer'
		];

		$data = $request->all();

		$validator = Validator::make($data, $rules);

		if ($validator->fails()) {
			return response()->json([
				'status' => 'Error',
				'message' => $validator->errors()
			], 400);
		}

		$chapterId = $request->input('chapter_id');
		$chapter = Chapters::find($chapterId);

		if (!$chapter) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Chapter not found'
			], 404);
		}

		$lesson = Lessons::create($data);

		return response()->json([
			'status' => 'Success',
			'data' => $lesson
		]);
	}

	public function update(Request $request, $id)
	{
		$rules = [
			'name' => 'string',
			'video' => 'string',
			'chapter_id' => 'integer'
		];

		$data = $request->all();

		$validator = Validator::make($data, $rules);

		if ($validator->fails()) {
			return response()->json([
				'status' => 'Error',
				'message' => $validator->errors()
			], 400);
		}

		$lesson = Lessons::find($id);

		if (!$lesson) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Lesson not found'
			], 404);
		}

		$chapterId = $request->input('chapter_id');

		if ($chapterId) {
			$chapter = Chapters::find($chapterId);

			if (!$chapter) {
				return response()->json([
					'status' => 'Error',
					'message' => 'Chapter not found'
				], 404);
			}
		}

		$lesson->fill($data);
		$lesson->save();

		return response()->json([
			'status' => 'Success',
			'data' => $lesson
		]);
	}

	public function destroy($id)
	{
		$lesson = Lessons::find($id);

		if (!$lesson) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Lesson not found'
			], 404);
		}

		$lesson->delete();

		return response()->json([
			'status' => 'Success',
			'message' => 'Lesson has been deleted'
		]);
	}
}
