<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocialMedia;
use App\Services\YouTubeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class YouTubeAccountController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function redirectToProvider(): RedirectResponse
    {
        $youtubeService = new YouTubeService();
        $url = $youtubeService->getRedirectUrl();
        return redirect()->away($url);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function handleProviderCallback(Request $request): RedirectResponse
    {
        $code = $request->input('code');

        $youtubeService = new YouTubeService();
        $authorization = $youtubeService->getAccessToken($code);

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
        $user->youtube_refresh_token = $refreshToken;
        $user->youtube_access_token = json_encode($authorization);
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
        $youtubeService = new YouTubeService($authorization);
        $result = $youtubeService->getProfile();
        $imageUrl = $result['items'][0]['snippet']['thumbnails']['default']['url'];

        if (Session::has('client_id')) {
            $clientId = Session::get('client_id');
        }
        if (auth('web')->id()) {
            $clientId = auth('web')->id();
        }
        $user = User::find($clientId);

        $userSocialMedia = UserSocialMedia::where([
            'client_id' => $user->id,
            'provider' => 'youtube',
        ])->first();

        if ($userSocialMedia) {
            $userSocialMedia->profile_picture = $imageUrl;
            $userSocialMedia->save();
        } else {
            UserSocialMedia::create([
                'client_id' => $user->id,
                'provider' => 'youtube',
                'profile_picture' => $imageUrl,
            ]);
        }

        if (!$user->brand_picture) {
            $user->brand_picture = $imageUrl;
        }

        return true;
    }
}
