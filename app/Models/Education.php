<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Education extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'education';

    protected $fillable = [
        'user_id',
        'school_name',
        'degree',
        'field_of_study',
        'start_date',
        'end_date',
        'description',
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
