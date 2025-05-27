<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\UserPaymentMethod;
use App\Services\StripeService;
use App\Traits\PackageTrait;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use PackageTrait;

    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Display the package purchase page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userPaymentMethods = UserPaymentMethod::where('user_id', auth('web')->id())
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
        $defaultPaymentMethod = $userPaymentMethods->firstWhere('is_default', true);
        if (!$defaultPaymentMethod) {
            $defaultPaymentMethod = $userPaymentMethods->first();
        }
        $packages = $this->indexPackage();

        return view('client.packages.index', compact('packages', 'userPaymentMethods', 'defaultPaymentMethod'));
    }

    public function buy(Request $request)
    {
        $request->merge([
            'customer_id' => auth('web')->user()->stripe_customer_id,
        ]);

        $package = $this->getPackageById($request->package_id);
        if (!$package) {
            return response()->json(['error' => 'Package not found'], 404);
        }

        $subscription = $this->stripeService->createSubscription($request->customer_id, $package->stripe_price_id);
        if ($subscription->status !== 'active') {
            return response()->json(['error' => 'Subscription creation failed'], 400);
        }

        $this->buyPackage($request);

        return redirect()->route('user.package.index')->with('success', 'Package purchased successfully.');
    }
}
