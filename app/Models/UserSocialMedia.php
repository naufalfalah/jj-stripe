<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSocialMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'provider',
        'profile_picture',
    ];
}
