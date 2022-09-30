<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\AmoCRM;

use App\Http\Controllers\Auth\AmoCRM\Traits\AccessTokenTrait;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Company;
use App\Services\AmoCrmService;

class CompanyController extends Controller
{
    use AccessTokenTrait;

    public function getCompanies(AmoCrmService $amoCrmService): \Illuminate\Http\RedirectResponse
    {
        try {
            $accessToken = $this->getToken();

            $apiClient = $amoCrmService->getApiClient()->setAccessToken($accessToken);

            $companies = $apiClient->companies()->get()->toArray();

            foreach ($companies as $company) {

                $account = Account::where('amocrm_id', $company['account_id'])->pluck('id');
                if($account->isEmpty()) {
                    return back()->with('error', 'Выгрузите сначала данные по Аккаунту');
                }
                $accountId = $account[0];

                Company::query()->upsert([
                    'amocrm_id' => $company['id'],
                    'name' => $company['name'],
                    'responsible_user_id' => $company['responsible_user_id'],
                    'account_id' =>$accountId,
                ], ['amocrm_id'], ['name', 'responsible_user_id', 'account_id']);
            }
            return back()->with('success', 'Успешно');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
