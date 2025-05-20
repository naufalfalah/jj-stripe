<?php

namespace App\Http\Controllers\Administrator;

use App\Constants\MenuConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\PackageRequest;
use App\Services\StripeService;
use App\Traits\PackageTrait;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    use PackageTrait;

    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

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

    public function store(PackageRequest $request)
    {
        $price = $this->stripeService->createPrice('prod_SL2R12wic4JMyW', $request->price, 'sgd');
        if (!$price) {
            return response()->json(['error' => 'Failed to create price'], 500);
        }

        $request->merge([
            'stripe_price_id' => $price->id,
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $request->merge(['logo' => $logoPath]);
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
