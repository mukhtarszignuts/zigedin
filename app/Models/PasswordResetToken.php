<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'password_reset_tokens';

    // Specify the primary key
    protected $primaryKey = 'email';

    protected $fillable = ['email', 'token'];

    public $timestamps = false;
}
