<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkExperience extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'work_experiences';

    protected $fillable = [
        'user_id',
        'title',
        'company_name',
        'employment_type',
        'start_date',
        'end_date',
        'description',
        'location',
        'location_type',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
