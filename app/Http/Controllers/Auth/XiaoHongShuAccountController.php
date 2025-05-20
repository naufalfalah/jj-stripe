<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\XiaoHongShuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class XiaoHongShuAccountController extends Controller
{
    public function redirectToProvider(): RedirectResponse
    {
        $xiaoHongShuService = new XiaoHongShuService;
        $url = $xiaoHongShuService->getRedirectUrl();

        return redirect()->away($url);
    }

    public function handleProviderCallback(Request $request): RedirectResponse
    {
        $code = $request->input('code');

        $xiaoHongShuService = new XiaoHongShuService;
        $authorization = $xiaoHongShuService->getAccessToken($code);

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
        $user->xiao_hong_shu_access_token = json_encode($authorization);
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
        $xiaoHongShuService = new XiaoHongShuService($authorization);
        // TODO: Revisit after xiao hong shu app
    }
}
