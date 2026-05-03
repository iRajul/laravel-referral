<?php

namespace Jijunair\LaravelReferral\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;

class ReferralController extends Controller
{
    /**
     * Assign a referral code to the user.
     */
    public function assignReferrer(string $referralCode): RedirectResponse
    {
        $refCookieName = (string) config('referral.cookie_name');
        $refCookieExpiry = (int) config('referral.cookie_expiry');

        if (Cookie::has($refCookieName)) {
            return redirect()->route(config('referral.redirect_route'));
        }

        $cookie = Cookie::make($refCookieName, $referralCode, $refCookieExpiry);

        return redirect()->route(config('referral.redirect_route'))->withCookie($cookie);
    }

    /**
     * Generate referral codes for existing users.
     */
    public function createReferralCodeForExistingUsers(): JsonResponse
    {
        $userModel = resolve(config('referral.user_model'));
        $users = $userModel::cursor();

        foreach ($users as $user) {
            if (! $user->hasReferralAccount()) {
                $user->createReferralAccount();
            }
        }

        return response()->json(['message' => 'Referral codes generated for existing users.']);
    }
}
