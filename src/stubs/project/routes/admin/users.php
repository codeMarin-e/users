<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserTwoFactorAuthController;
use App\Models\User;

//CHECK https://spatie.be/docs/laravel-permission/v5/basic-usage/middleware

Route::group([
    'controller' => UserController::class,
    'middleware' => ['auth:admin', 'can:view,'.User::class],
    'as' => 'users.', //naming prefix
    'prefix' => 'users', //for routes
], function() {
    Route::get('', 'index')->name('index');
    Route::get('xlsx', 'index')->name('index_xlsx')->defaults('xlsx', true);
    Route::post('', 'store')->name('store')->middleware('can:create,'.User::class);
    Route::get('create', 'create')->name('create')->middleware('can:create,'.User::class);
    Route::get('{chUser}/edit', 'edit')->name('edit');
    Route::get('{chUser}', 'edit')->name('show');
    Route::patch('{chUser}', 'update')->name('update')->middleware('can:update,chUser');
    Route::delete('{chUser}', 'destroy')->name('destroy')->middleware('can:delete,chUser');

    //TWO FACTOR AUTH
    Route::post('/user/two-factor-authentication/{chUser}', [UserTwoFactorAuthController::class, 'store'])
        ->middleware('can:update,chUser')
        ->name('two-factor.enable');
    Route::delete('/user/two-factor-authentication/{chUser}', [UserTwoFactorAuthController::class, 'destroy'])
        ->middleware('can:update,chUser')
        ->name('two-factor.disable');
    Route::post('/user/two-factor-recovery-codes/{chUser}', [UserTwoFactorAuthController::class, 'recoveryCodes'])
        ->middleware('can:update,chUser')
        ->name('two-factor.recovery-codes');
    //END TWO FACTOR AUTH

    // @HOOK_USERS_ROUTES
});
