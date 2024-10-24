<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Connection extends Model
{
    use HasFactory;

    protected $table = 'connections';

    protected $fillable = [
        'user_id',
        'connection_id',
        'status',
        'request_date',
    ];

    public $timestamps = false;

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function friend()
    {
        return $this->belongsTo(User::class, 'connection_id');
    }
}
