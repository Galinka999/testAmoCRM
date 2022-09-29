<?php

use App\Http\Controllers\Auth\AmoCRM\LeadController;
use App\Http\Controllers\Auth\AmoCRM\OAuthController;
use Illuminate\Support\Facades\Route;




Route::get('/redirect', [OAuthController::class, 'redirect']);
Route::get('/callback', [OAuthController::class, 'callback']);

Route::get('/leads', [LeadController::class, 'getLeads']);
