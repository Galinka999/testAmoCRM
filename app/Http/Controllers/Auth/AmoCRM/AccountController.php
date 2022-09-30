<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\AmoCRM;

use AmoCRM\Models\AccountModel;
use App\Http\Controllers\Auth\AmoCRM\Traits\AccessTokenTrait;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\AmoCrmService;

class AccountController extends Controller
{
    use AccessTokenTrait;

    public function getAccount(AmoCrmService $amoCrmService): \Illuminate\Http\RedirectResponse
    {
        $accessToken = $this->getToken();

        $apiClient = $amoCrmService->getApiClient()->setAccessToken($accessToken);

        $data = $apiClient->account()->getCurrent(AccountModel::getAvailableWith());

//        if(is_null($data)) {
//            return back()->with('error', 'К сожалению, выгружать пока нечего.');
//        }

        Account::query()->updateOrCreate([
            'amocrm_id' => $data->id,
            'name' => $data->name,
            'subdomain' => $data->subdomain,
        ]);

        return back()->with('success', 'Успешно');
    }
}
