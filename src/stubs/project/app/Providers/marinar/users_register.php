<?php

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

Route::model('chUser', User::class);
Gate::policy(User::class, UserPolicy::class);

