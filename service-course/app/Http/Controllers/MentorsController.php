<?php

namespace App\Http\Controllers;

use App\Models\Mentors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MentorsController extends Controller
{
	public function index()
	{
		$mentors = Mentors::all();

		return response()->json([
			'status' => 'Success',
			'data' => $mentors
		]);
	}

	public function show($id)
	{
		$mentor = Mentors::find($id);

		if (!$mentor) {
			return response()->json([
				'status' => "Error",
				'message' => 'Mentor not found'
			], 404);
		}

		return response()->json([
			'status' => 'Success',
			'data' => $mentor
		]);
	}

	public function create(Request $request)
	{
		$rules = [
			'name' => 'required|string',
			'profile' => 'required|url',
			'profession' => 'required|string',
			'email' => 'required|email',
		];

		$data = $request->all();

		$validator = Validator::make($data, $rules);

		if ($validator->fails()) {
			return response()->json([
				'status' => 'Error',
				'message' => $validator->errors()
			], 400);
		}

		$mentor = Mentors::create($data);

		return response()->json([
			'status' => 'Success',
			'data' => $mentor
		]);
	}

	public function update(Request $request, $id)
	{
		$rules = [
			'name' => 'string',
			'profile' => 'url',
			'profession' => 'string',
			'email' => 'email',
		];

		$data = $request->all();

		$validator = Validator::make($data, $rules);

		if ($validator->fails()) {
			return response()->json([
				'status' => 'Error',
				'message' => $validator->errors()
			], 400);
		}

		$mentor = Mentors::find($id);

		if (!$mentor) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Mentor not found'
			], 404);
		}

		$mentor->fill($data);

		$mentor->save();

		return response()->json([
			'status' => 'Success',
			'data' => $mentor
		]);
	}

	public function destroy($id)
	{
		$mentor = Mentors::find($id);

		if (!$mentor) {
			return response()->json([
				'status' => 'Error',
				'message' => 'Mentor not found'
			], 404);
		}

		$mentor->delete();

		return response()->json([
			'status' => 'Success',
			'message' => 'Mentor has been deleted'
		]);
	}
}
