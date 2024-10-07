<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;

    protected $table = 'connections';

    protected $fillable = [
        'user_id',
        'connection_id',
        'status',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function connection()
    {
        return $this->belongsTo(User::class, 'connection_id');
    }
}
