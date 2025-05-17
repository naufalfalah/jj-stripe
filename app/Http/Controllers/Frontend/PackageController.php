<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Traits\PackageTrait;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use PackageTrait;

    public function buy(Request $request)
    {
        return $this->buyPackage($request);
    }
}
