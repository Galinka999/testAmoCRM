<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\AmoCRM;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AccountController extends Controller
{
    public function getAccount(): \Illuminate\Http\RedirectResponse
    {
        $token = Token::query()->latest()->first();
        $access_token = $token->access_token;

        $api = HTTP::withToken($access_token)->get('https://galina89ruzhyk.amocrm.ru/api/v4/account');
        $data = json_decode((string)$api, true);

        if(is_null($data)) {
            return back()->with('error', 'К сожалению, выгружать пока нечего.');
        }

        Account::query()->updateOrCreate([
            'amocrm_id' => $data['id'],
            'name' => $data['name'],
            'subdomain' => $data['subdomain'],
        ]);
        return back()->with('success', 'Успешно');
    }
}
