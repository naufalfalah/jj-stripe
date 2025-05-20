<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TikTokAdsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TikTokAdsAccountController extends Controller
{
    public function redirectToProvider(): RedirectResponse
    {
        $tiktokAdsService = new TikTokAdsService;
        $url = $tiktokAdsService->getRedirectUrl();

        return redirect()->away($url);
    }

    public function handleProviderCallback(Request $request): RedirectResponse
    {
        $code = $request->query('code');
        $tiktokAdsService = new TikTokAdsService;
        $authorization = $tiktokAdsService->getAccessToken($code);

        if (Session::has('client_id')) {
            $clientId = Session::get('client_id');
            $route = 'admin.social_account.index';
        }
        if (auth('web')->id()) {
            $clientId = auth('web')->id();
            $route = 'user.social_account.index';
        }

        if (!isset($authorization['access_token']) || !$authorization['access_token']) {
            return redirect()->route($route)->with('error', 'Oauth failed');
        }

        $user = User::find($clientId);
        $user->tiktok_ads_access_token = json_encode($authorization['data']);
        $user->save();

        return redirect()->route($route);
    }
}
