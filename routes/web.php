<?php

use App\Http\Controllers\Auth\AmoCRM\AccountController;
use App\Http\Controllers\Auth\AmoCRM\CompanyController;
use App\Http\Controllers\Auth\AmoCRM\ContactController;
use App\Http\Controllers\Auth\AmoCRM\LeadController;
use App\Http\Controllers\Auth\AmoCRM\OAuthController;
use App\Http\Controllers\Auth\AmoCRM\PipelineController;
use App\Http\Controllers\Auth\AmoCRM\SourceController;
use App\Http\Controllers\Auth\AmoCRM\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/redirect', [OAuthController::class, 'redirect'])->name('redirect');
Route::get('/callback', [OAuthController::class, 'callback']);

Route::get('/leads', [LeadController::class, 'getLeads'])->name('leads');
Route::get('/account', [AccountController::class, 'getAccount'])->name('account');
Route::get('/users', [UserController::class, 'getUsers'])->name('users');
Route::get('/contacts', [ContactController::class, 'getContacts'])->name('contacts');
Route::get('/companies', [CompanyController::class, 'getCompanies'])->name('companies');
Route::get('/pipelines', [PipelineController::class, 'getPipelines'])->name('pipeline');
Route::get('/sources', [SourceController::class, 'getSources'])->name('sources');
