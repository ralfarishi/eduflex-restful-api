<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use App\Models\Mentors;
use App\Models\Reviews;
use App\Models\Chapters;
use App\Models\MyCourses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CoursesController extends Controller
{
	public function index(Request $request)
	{
		$courses = Courses::query();

		$q = $request->query('q');
		$status = $request->query('status');

		// filter search
		$courses->when($q, function ($query) use ($q) {
			return $query->whereRaw("name LIKE '%" . strtolower($q) . "%'");
		});

		// filter course status
		$courses->when($status, function ($query) use ($status) {
			return $query->where('status', '=', $status);
		});

		return response()->json([
			'status' => 'Success',
			'data' => $courses->paginate(5)
		]);
	}

	public function show($id)
	{
		$course = Courses::with('chapters.lessons', 'mentor', 'images')->find($id);

		if (!$course) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Course not found'
			], 404);
		}

		$reviews = Reviews::where('course_id', '=', $id)->get()->toArray();
		if (count($reviews) > 0) {
			$userIds = array_column($reviews, 'user_id');
			$users = getUserByIds($userIds);

			if ($users['status'] === 'Error') {
				$reviews = [];
			} else {
				foreach ($reviews as $key => $review) {
					$userIndex = array_search($review['user_id'], array_column($users['data'], 'id'));
					$reviews[$key]['users'] = $users['data'][$userIndex];
				}
			}
		}

		$totalStudent = MyCourses::where('course_id', '=', $id)->count();
		$totalVideos = Chapters::where('course_id', '=', $id)->withCount('lessons')->get()->toArray();
		$sumTotalVideos = array_sum(array_column($totalVideos, 'lessons_count'));

		$course['reviews'] = $reviews;
		$course['total_videos'] = $sumTotalVideos;
		$course['total_student'] = $totalStudent;

		return response()->json([
			'status' => 'Success',
			'data' => $course
		], 200);
	}

	public function create(Request $request)
	{
		$rules = [
			'name' => 'required|string',
			'certificate' => 'required|boolean',
			'thumbnail' => 'string|url',
			'type' => 'required|in:free,premium',
			'status' => 'required|in:draft,published',
			'price' => 'integer',
			'level' => 'required|in:all-level,beginner,intermediate,advance',
			'mentor_id' => 'required|integer',
			'description' => 'string',
		];

		$data = $request->all();

		$validator = Validator::make($data, $rules);

		if ($validator->fails()) {
			return response()->json([
				'status' => 'Error',
				'message' => $validator->errors()
			], 400);
		}

		$mentorId = $request->input('mentor_id');
		$mentor = Mentors::find($mentorId);

		if (!$mentor) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Mentor not found'
			], 404);
		}

		$course = Courses::create($data);

		return response()->json([
			'status' => 'Success',
			'data' => $course
		]);
	}

	public function update(Request $request, $id)
	{
		$rules = [
			'name' => 'string',
			'certificate' => 'boolean',
			'thumbnail' => 'string|url',
			'type' => 'in:free,premium',
			'status' => 'in:draft,published',
			'price' => 'integer',
			'level' => 'in:all-level,beginner,intermediate,advance',
			'mentor_id' => 'integer',
			'description' => 'string',
		];

		$data = $request->all();

		$validator = Validator::make($data, $rules);

		if ($validator->fails()) {
			return response()->json([
				'status' => 'Error',
				'message' => $validator->errors()
			], 400);
		}

		$course = Courses::find($id);

		if (!$course) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Course not found'
			], 404);
		}

		$mentorId = $request->input('mentor_id');

		if ($mentorId) {
			$mentor = Mentors::find($mentorId);

			if (!$mentor) {
				return response()->json([
					'status' => 'Error',
					'message' => 'Mentor not found'
				], 404);
			}
		}

		$course->fill($data);
		$course->save();

		return response()->json([
			'status' => 'Success',
			'data' => $course
		]);
	}

	public function destroy($id)
	{
		$course = Courses::find($id);

		if (!$course) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Course not found'
			], 404);
		}

		$course->delete();

		return response()->json([
			'status' => 'Success',
			'message' => 'Course has been deleted'
		]);
	}
}
