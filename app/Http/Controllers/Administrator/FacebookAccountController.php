<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FacebookAccessToken;
use Socialite;

class FacebookAccountController extends Controller
{
    public function index()
    {
        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
        $data = [
            'breadcrumb_main' => 'Settings',
            'breadcrumb' => 'Facebook Account Connectivity',
            'title' => 'Facebook Account Connectivity',
        ];

        return view('admin.settings.facebook_connect')->with($data);
    }

    // public function redirectToFacebook()
    // {
    //     return Socialite::driver('facebook')->redirect();
    // }

    // public function oauth()
    // {

    //     try {
    //         $user = Socialite::driver('facebook')->user();
    //         $fb_access_token = new FacebookAccessToken;
    //         $fb_access_token->admin_id = auth('admin')->user()->id;
    //         $fb_access_token->access_token = $user->token;
    //         $fb_access_token->save();

    //     } catch (\Exception $e) {
    //         return redirect()->route('admin.setting.facebook_account');
    //     }

    //     return redirect()->route('admin.setting.facebook_account');
    // }
}
