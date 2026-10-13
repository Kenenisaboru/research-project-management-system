<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class TwoFactorController
 *
 * Handles Time-Based One-Time Password (TOTP) setup, activation, challenges, and recovery codes.
 */
class TwoFactorController extends Controller
{
    /**
     * Show the 2FA configuration and setup portal.
     */
    public function setup()
    {
        $user = Auth::user();

        if (!$user->two_factor_secret) {
            $user->two_factor_secret = TotpService::generateSecret();
            $user->save();
        }

        $otpUrl = TotpService::getOtpAuthUrl($user->email, $user->two_factor_secret);

        return view('auth.two_factor_setup', [
            'user'         => $user,
            'secret'       => $user->two_factor_secret,
            'otpUrl'       => $otpUrl,
            'enabled'      => (bool) $user->two_factor_enabled,
        ]);
    }

    /**
     * Confirm TOTP setup with a valid 6-digit code and generate recovery codes.
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        if (!TotpService::verify($user->two_factor_secret, $request->code)) {
            return back()->withErrors(['code' => __('Invalid verification code. Please check your authenticator clock and try again.')]);
        }

        $plainRecoveryCodes = TotpService::generateRecoveryCodes(8);
        $hashedRecoveryCodes = array_map(fn ($code) => Hash::make(str_replace('-', '', strtoupper($code))), $plainRecoveryCodes);

        $user->update([
            'two_factor_enabled'        => true,
            'two_factor_confirmed_at'   => now(),
            'two_factor_recovery_codes' => json_encode($hashedRecoveryCodes),
        ]);

        AuditService::log('2FA_ENABLED', 'User', $user->id, "User {$user->name} successfully enabled TOTP Two-Factor Authentication", $user->id);

        return view('auth.two_factor_recovery', [
            'recoveryCodes' => $plainRecoveryCodes,
        ])->with('success', __('Two-Factor Authentication is now active on your account.'));
    }

    /**
     * Disable 2FA with mandatory password re-authentication.
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => __('Incorrect password. 2FA cannot be disabled without valid credentials.')]);
        }

        $user->update([
            'two_factor_enabled'        => false,
            'two_factor_secret'         => null,
            'two_factor_confirmed_at'   => null,
            'two_factor_recovery_codes' => null,
        ]);

        AuditService::log('2FA_DISABLED', 'User', $user->id, "User {$user->name} disabled Two-Factor Authentication", $user->id);

        return redirect()->route('profile')->with('success', __('Two-Factor Authentication has been disabled.'));
    }

    /**
     * Show the 2FA challenge screen during login.
     */
    public function showChallenge(Request $request)
    {
        if (!session()->has('2fa:user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two_factor_challenge');
    }

    /**
     * Verify the 2FA code or recovery code to complete login.
     */
    public function verifyChallenge(Request $request)
    {
        $userId = session('2fa:user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        // Verification via 6-digit TOTP code
        if ($request->filled('code')) {
            $code = str_replace(' ', '', $request->code);
            if (TotpService::verify($user->two_factor_secret, $code)) {
                return $this->completeLogin($user, $request);
            }
        }

        // Verification via 10-char recovery code
        if ($request->filled('recovery_code')) {
            $cleanCode = str_replace(['-', ' '], '', strtoupper($request->recovery_code));
            $recoveryCodes = json_decode($user->two_factor_recovery_codes ?? '[]', true);

            foreach ($recoveryCodes as $index => $hashedCode) {
                if (Hash::check($cleanCode, $hashedCode)) {
                    // Consume the single-use recovery code
                    unset($recoveryCodes[$index]);
                    $user->two_factor_recovery_codes = json_encode(array_values($recoveryCodes));
                    $user->save();

                    AuditService::log('2FA_RECOVERY_USED', 'User', $user->id, "User {$user->name} authenticated using an emergency recovery code", $user->id);

                    return $this->completeLogin($user, $request);
                }
            }
        }

        return back()->withErrors([
            'code' => __('Invalid authentication code or emergency recovery code.'),
        ]);
    }

    /**
     * Complete the authenticated session.
     */
    protected function completeLogin(User $user, Request $request)
    {
        session()->forget('2fa:user_id');
        Auth::login($user, session('2fa:remember', false));
        session()->forget('2fa:remember');

        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        AuditService::log('LOGIN_2FA', 'User', $user->id, "User {$user->name} passed 2FA challenge and logged in successfully", $user->id);

        return redirect()->intended('/dashboard');
    }
}
