<?php

namespace App\Models;

use App\Http\Traits\CommonFunctionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employer extends Model
{
    use HasFactory, SoftDeletes , CommonFunctionTrait;

    protected $table = 'employers';

    protected $fillable = [
        'type',
        'user_id',
        'name',
        'industry',
        'logo',
        'location',
        'website',
        'organization_size',
        'organization_type',
        'public_url',
        'tagline',
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

    public function getPublicUrlAttribute($value)
    {
        if($value){
            return url('/company/' . $value);
        }
    }

    
    public function getOrganizationTypeAttribute($value)
    {
        return $this->organisationType($value);
    }
}
