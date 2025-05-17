<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocialMedia;
use App\Services\LinkedInService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LinkedInAccountController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function redirectToProvider(): RedirectResponse
    {
        $linkedinService = new LinkedInService();
        $url = $linkedinService->getRedirectUrl();
        return redirect()->away($url);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function handleProviderCallback(Request $request): RedirectResponse
    {
        $code = $request->query('code');

        $linkedinService = new LinkedInService();
        $authorization = $linkedinService->getAccessToken($code);

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
        $user->linkedin_access_token = json_encode($authorization);
        $user->save();

        $this->storeUserSocialMedia($authorization);

        return redirect()->route($route);
    }

    /**
     * @param string $authorization
     *
     * @return bool
     */
    public function storeUserSocialMedia($authorization)
    {
        $linkedinService = new LinkedInService($authorization);
        $result = $linkedinService->getUserInfo();
        $imageUrl = $result['picture'];

        if (Session::has('client_id')) {
            $clientId = Session::get('client_id');
        }
        if (auth('web')->id()) {
            $clientId = auth('web')->id();
        }
        $user = User::find($clientId);

        $userSocialMedia = UserSocialMedia::where([
            'client_id' => $user->id,
            'provider' => 'linkedin',
        ])->first();

        if ($userSocialMedia) {
            $userSocialMedia->profile_picture = $imageUrl;
            $userSocialMedia->save();
        } else {
            UserSocialMedia::create([
                'client_id' => $user->id,
                'provider' => 'linkedin',
                'profile_picture' => $imageUrl,
            ]);
        }

        if (!$user->brand_picture) {
            $user->brand_picture = $imageUrl;
        }

        return true;
    }
}
