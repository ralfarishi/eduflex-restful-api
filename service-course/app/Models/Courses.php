<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
	protected $table = 'courses';

	protected $casts = [
		'created_at' => 'datetime:Y-m-d H:m:s',
		'updated_at' => 'datetime:Y-m-d H:m:s'
	];

	protected $fillable = [
		'name', 'certificate', 'thumbnail', 'type', 'status', 'price', 'level', 'description', 'mentor_id'
	];

	public function mentor()
	{
		return $this->belongsTo(Mentors::class);
	}

	public function chapters()
	{
		return $this->hasMany(Chapters::class)->orderBy('id', 'ASC');
	}

	public function images()
	{
		return $this->hasMany(ImageCourses::class)->orderBy('id', 'DESC');
	}
}
