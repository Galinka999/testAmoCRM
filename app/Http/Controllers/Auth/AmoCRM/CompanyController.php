<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\AmoCRM;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Company;
use App\Models\LeadPipeline;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CompanyController extends Controller
{
    public function getCompanies(): \Illuminate\Http\RedirectResponse
    {
        $token = Token::query()->latest()->first();
        $access_token = $token->access_token;

        $api = HTTP::withToken($access_token)->get('https://galina89ruzhyk.amocrm.ru/api/v4/companies');
        $data = json_decode((string)$api, true);

        if(is_null($data)) {
            return back()->with('error', 'К сожалению, выгружать пока нечего.');
        }

        $companies = $data['_embedded']['companies'];

        foreach ($companies as $company) {

            $account = Account::where('amocrm_id', $company['account_id'])->pluck('id');
            if($account->isEmpty()) {
                return back()->with('error', 'Выгрузите сначала данные по Аккаунту');
            }
            $accountId = $account[0];

            Company::query()->updateOrCreate([
                'amocrm_id' => $company['id'],
                'name' => $company['name'],
                'responsible_user_id' => $company['responsible_user_id'],
                'account_id' =>$accountId,
            ]);
        }
        return back()->with('success', 'Успешно');
    }
}
