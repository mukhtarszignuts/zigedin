<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory, SoftDeletes, HasApiTokens, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'city',
        'headline',
        'summary',
        'role',
        'is_active',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',

    ];

    // apend value 
    protected $appends = ['image_url', 'connection_count'];

    public function getImageUrlAttribute()
    {
        if ($this->profile_image) {
            return asset('storage/profile_images/' . $this->profile_image);
        }
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills', 'user_id', 'skill_id');
    }

    public function experiences()
    {
        return $this->hasMany(WorkExperience::class);
    }

    // employer 
    public function employer()
    {
        return $this->hasOne(Employer::class);
    }

    // connection 
    public function connections()
    {
        return $this->belongsToMany(User::class, 'connections', 'user_id', 'connection_id')->withPivot('status', 'request_date')->select('id', 'first_name', 'last_name', 'profile_image');
    }

    // invite connection 
    public function inviteConnections()
    {
        return $this->belongsToMany(User::class, 'connections', 'connection_id', 'user_id')->withPivot('status', 'request_date')->select('id', 'first_name', 'last_name', 'profile_image');
    }

    // connection count 
    public function getConnectionCountAttribute()
    {
        return $this->connections()->count();
    }

    // educations  
    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    // Relationship for posts authored by the user
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }
}
