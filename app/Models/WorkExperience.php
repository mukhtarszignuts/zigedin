<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkExperience extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'work_experience';

    protected $fillable = [
        'user_id',
        'job_title',
        'company_name',
        'start_date',
        'end_date',
        'description',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
