<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GoogleBusinessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GoogleBusinessAccountController extends Controller
{
    public function redirectToProvider(): RedirectResponse
    {
        $googleBusinessService = new GoogleBusinessService;
        $url = $googleBusinessService->getRedirectUrl();

        return redirect()->away($url);
    }

    public function handleProviderCallback(Request $request): RedirectResponse
    {
        $code = $request->input('code');

        $googleBusinessService = new GoogleBusinessService;
        $authorization = $googleBusinessService->getAccessToken($code);

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
        $refreshToken = json_encode($authorization['refresh_token']);
        $refreshToken = str_replace('\/\/', '//', $refreshToken);
        $refreshToken = trim($refreshToken, '"');
        $user->google_business_refresh_token = $refreshToken;
        $user->google_business_access_token = json_encode($authorization);
        $user->save();

        $this->storeUserSocialMedia($authorization);

        return redirect()->route($route);
    }

    /**
     * @param string $authorization
     * @return bool
     */
    public function storeUserSocialMedia($authorization)
    {
        $googleBusinessService = new GoogleBusinessService($authorization);
        // TODO: Revisit after google business app

        return true;
    }
}
