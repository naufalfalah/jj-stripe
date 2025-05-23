<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designer extends Model
{
    use DianujHashidsTrait, HasFactory, SoftDeletes;

    protected $fillable = [

    ];
}
