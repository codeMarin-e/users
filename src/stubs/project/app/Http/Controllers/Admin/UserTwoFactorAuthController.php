<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Laravel\Fortify\Contracts\RecoveryCodesGeneratedResponse;
use Laravel\Fortify\Contracts\TwoFactorDisabledResponse;
use Laravel\Fortify\Contracts\TwoFactorEnabledResponse;

class UserTwoFactorAuthController extends Controller {

    /**
     * Enable two factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Fortify\Actions\EnableTwoFactorAuthentication  $enable
     * @return \Laravel\Fortify\Contracts\TwoFactorEnabledResponse
     */
    public function store(User $chUser, Request $request, EnableTwoFactorAuthentication $enable)
    {
        $enable($chUser);

        return app(TwoFactorEnabledResponse::class);
    }

    /**
     * Disable two factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Fortify\Actions\DisableTwoFactorAuthentication  $disable
     * @return \Laravel\Fortify\Contracts\TwoFactorDisabledResponse
     */
    public function destroy(User $chUser, Request $request, DisableTwoFactorAuthentication $disable)
    {
        $disable($chUser);

        return app(TwoFactorDisabledResponse::class);
    }


    /**
     * Generate a fresh set of two factor authentication recovery codes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Fortify\Actions\GenerateNewRecoveryCodes  $generate
     * @return \Laravel\Fortify\Contracts\RecoveryCodesGeneratedResponse
     */
    public function recoveryCodes(User $chUser, Request $request, GenerateNewRecoveryCodes $generate)
    {
        $generate($chUser);

        return app(RecoveryCodesGeneratedResponse::class);
    }
}
