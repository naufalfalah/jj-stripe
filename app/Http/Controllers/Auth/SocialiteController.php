<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ClientTour;
use App\Models\Tour;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Socialite;

class SocialiteController extends Controller
{
    public function redirectToProvider(Request $request, $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            $this->loginOrRegisterUser($socialUser, $provider);

            $route = $this->loginRoute();

            return redirect()->route($route);
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Failed to login with '.ucfirst($provider));
        }
    }

    private function loginOrRegisterUser($socialUser, $provider)
    {
        $user = User::where('email', $socialUser->getEmail())
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);
        }

        Auth::guard('web')->login($user);
    }

    private function loginRoute()
    {
        $route = 'user.dashboard';

        $accessibleMenus = auth('web')->user()->getAccessibleMenus();

        if (in_array('wallet', $accessibleMenus)) {
            $route = 'user.wallet.add';

            $tour = Tour::firstOrCreate(['code' => 'FINISH_2'], ['name' => 'Finish']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
            if (!$client_tour) {
                $route = 'user.ads.all';
            }

            $tour = Tour::firstOrCreate(['code' => 'FINISH_1'], ['name' => 'Finish']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
            if (!$client_tour) {
                $route = 'user.wallet.transfer_funds';
            }

            $tour = Tour::firstOrCreate(['code' => 'AFTER_TOPUP'], ['name' => 'After Topup']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
            if (!$client_tour) {
                $route = 'user.wallet.transaction_report';
            }

            $tour = Tour::firstOrCreate(['code' => 'START_3'], ['name' => 'Get Started']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
            if (!$client_tour) {
                $route = 'user.wallet.add';
            }

            $tour = Tour::firstOrCreate(['code' => 'START_2'], ['name' => 'Get Started']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
            if (!$client_tour) {
                $route = 'user.ads.add';
            }

            $tour = Tour::firstOrCreate(['code' => 'START_1'], ['name' => 'Get Started']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
            if (!$client_tour) {
                $route = 'user.wallet.add';
            }
        }

        return $route;
    }
}
