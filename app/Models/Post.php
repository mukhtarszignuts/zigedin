<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'content',
        'tags',
        'mentions',
        'is_active',
        'is_attachment',
        'visibility',

    ];

    protected $hidden = [
        'deleted_at'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'post_likes', 'post_id', 'user_id')->select('id','first_name','last_name','profile_image');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'post_id');
    }
}
