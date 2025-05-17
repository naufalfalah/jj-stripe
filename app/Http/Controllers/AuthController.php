<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //

    public function showOtpForm($id)
    {
        return view('auth.otp_verify', ['id' => $id]);
    }

    public function verifyOtp(Request $request)
    {
        $rules = [
            'otp' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }
        $user_id = hashids_decode($request->id);

        $user = UserOtp::where('user_id', $user_id)->where('is_expired', 0)->latest()->first();
        if ($user->otp != $request->otp) {
            $msg = [
                'errors' => [
                    'otp' => ['Invalid OTP. Please try again.'],
                ],
            ];

            return response()->json($msg);
        }
        $createdAt = Carbon::parse($user->created_at);
        $now = Carbon::now();
        if ($createdAt->diffInMinutes($now) >= 15) {
            UserOtp::where('user_id', $user_id)->update(['is_expired' => 1]);

            return response()->json([
                'errors' => [
                    'time' => ['More than 15 minutes have passed.'],
                ],
            ]);
        }
        if ($user->otp == $request->otp) {
            $user->is_expired = 1;
            $user->save();
            $user = User::find($user_id);
            $user->email_verified_at = now();
            $user->save();

            $msg = [
                'success' => 'Email verified successfully!',
                'redirect' => route('auth.login'),
            ];

            return response()->json($msg);
        } else {
            $msg = [
                'errors' => [
                    'otp' => ['Invalid OTP. Please try again.'],
                ],
            ];

            return response()->json($msg);
        }
    }

    public function resendOtp(Request $request)
    {
        $user_id = hashids_decode($request->user_id);
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $userOtp = new UserOtp;
        // Generate a new OTP
        $newOtp = rand(100000, 999999);
        $userOtp->user_id = $user_id;
        $userOtp->otp = $newOtp;

        $userOtp->is_expired = 0;
        $userOtp->save();

        $user = User::find($user_id);

        $data = [
            'otp' => $newOtp,
            'name' => $user->client_name,
        ];

        Mail::send('emails.otp_verification', $data, function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Verify Your Email to Complete Registration');
        });

        return response()->json(['success' => 'OTP has been resent to your email.']);
    }

    public function resendEmail($id)
    {
        $user_id = hashids_decode($id);
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $userOtp = new UserOtp;
        // Generate a new OTP
        $newOtp = rand(100000, 999999);
        $userOtp->user_id = $user_id;
        $userOtp->otp = $newOtp;

        $userOtp->is_expired = 0;
        $userOtp->save();

        $user = User::find($user_id);

        $data = [
            'otp' => $newOtp,
            'name' => $user->client_name,
        ];

        Mail::send('emails.otp_verification', $data, function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Verify Your Email to Complete Registration');
        });

        return redirect()->route('auth.otp.verify', ['id' => $id]);
    }
}
