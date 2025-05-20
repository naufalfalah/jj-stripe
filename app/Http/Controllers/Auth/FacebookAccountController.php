<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocialMedia;
use App\Services\FacebookPostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FacebookAccountController extends Controller
{
    public function redirectToProvider(): RedirectResponse
    {
        $facebookService = new FacebookPostService;
        $url = $facebookService->getRedirectUrl();

        return redirect()->away($url);
    }

    public function handleProviderCallback(Request $request): RedirectResponse
    {
        $code = $request->query('code');

        $facebookService = new FacebookPostService;
        $authorization = $facebookService->getAccessToken($code);

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
        $user->facebook_access_token = json_encode($authorization);
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
        $facebookService = new FacebookPostService($authorization);
        $result = $facebookService->getProfilePicture();
        $imageUrl = $result['data']['url'];

        if (Session::has('client_id')) {
            $clientId = Session::get('client_id');
        }
        if (auth('web')->id()) {
            $clientId = auth('web')->id();
        }
        $user = User::find($clientId);

        $userSocialMedia = UserSocialMedia::where([
            'client_id' => $user->id,
            'provider' => 'facebook',
        ])->first();

        if ($userSocialMedia) {
            $userSocialMedia->profile_picture = $imageUrl;
            $userSocialMedia->save();
        } else {
            UserSocialMedia::create([
                'client_id' => $user->id,
                'provider' => 'facebook',
                'profile_picture' => $imageUrl,
            ]);
        }

        if (!$user->brand_picture) {
            $user->brand_picture = $imageUrl;
        }

        return true;
    }
}
