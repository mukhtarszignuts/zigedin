<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jobs';

    protected $fillable = [
        'emp_id',
        'job_type',
        'work_mode',
        'title',
        'description',
        'location',
        'salary_range',
        'posted_at',
        'status',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
        'job_type' => 'string',
        'work_mode' => 'string',
        'status' => 'string',
    ];

    // Define relationships
    public function employer()
    {
        return $this->belongsTo(Employer::class, 'emp_id');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_skills', 'job_id', 'skill_id');
    }
}
