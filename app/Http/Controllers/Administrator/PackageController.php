<?php

namespace App\Http\Controllers\Administrator;

use App\Constants\MenuConstant;
use App\Http\Controllers\Controller;
use App\Traits\PackageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    use PackageTrait;

    public function index()
    {
        if (Auth::user('admin')->can('package-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $packages = $this->indexPackage();

        return view('admin.package.index', [
            'breadcrumb_main' => 'All Packages',
            'breadcrumb' => 'All Packages',
            'title' => 'All Packages',
            'packages' => $packages,
            'menus' => MenuConstant::MENUS,
        ]);
    }

    public function store(Request $request)
    {
        if (Auth::user('admin')->can('package-write') != true) {
            abort(403, 'Unauthorized action.');
        }

        $this->storePackage($request);

        return response()->json([
            'success' => 'Package Added Successfully',
            'reload' => true,
        ]);
    }

    public function edit($id)
    {

        if (Auth::user('admin')->can('package-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $packages = $this->indexPackage();
        $package = $this->getPackageById($id);

        return view('admin.package.index', [
            'breadcrumb_main' => 'All Packages',
            'breadcrumb' => 'All Packages',
            'title' => 'All Packages',
            'packages' => $packages,
            'menus' => MenuConstant::MENUS,
            'edit' => $package,
        ]);
    }
}
