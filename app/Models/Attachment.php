<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'attachments';

    protected $fillable = [
        'message_id',
        'post_id',
        'directory',
        'file_name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // apend value 
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->file_name) {
            return asset('storage/'.$this->directory.'/' . $this->file_name);
        }
    }

    // Relationships
    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
