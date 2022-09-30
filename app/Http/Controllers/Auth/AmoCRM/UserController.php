<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\AmoCRM;

use App\Http\Controllers\Controller;
use App\Models\ResponsibleUser;
use App\Models\Token;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function getUsers(): \Illuminate\Http\RedirectResponse
    {
        $token = Token::query()->latest()->first();
        $access_token = $token->access_token;

        $api = HTTP::withToken($access_token)->get('https://galina89ruzhyk.amocrm.ru/api/v4/users');
        $data = json_decode((string)$api, true);

        if(is_null($data)) {
            return back()->with('error', 'К сожалению, выгружать пока нечего.');
        }

        $users = $data['_embedded']['users'];

        foreach ($users as $user) {
            ResponsibleUser::query()->updateOrCreate([
                'amocrm_id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
            ]);
        }
        return back()->with('success', 'Успешно');
    }
}
