<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'menu',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
