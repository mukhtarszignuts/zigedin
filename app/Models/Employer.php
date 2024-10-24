<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employer extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'employers';

    protected $fillable = [
        'user_id',
        'name',
        'industry',
        'logo',
        'location',
        'website',
    ];
    // apend value 
    protected $appends = ['image_url'];

     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
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

     public function getImageUrlAttribute()
     {
         if ($this->logo) {
             return asset('storage/employer_images/' . $this->logo);
         }
     }
}
