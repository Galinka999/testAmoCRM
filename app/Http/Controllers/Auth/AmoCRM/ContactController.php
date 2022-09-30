<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\AmoCRM;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Contact;
use App\Models\ResponsibleUser;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    public function getContacts(): \Illuminate\Http\RedirectResponse
    {
        $token = Token::query()->latest()->first();
        $access_token = $token->access_token;

        $api = HTTP::withToken($access_token)->get('https://galina89ruzhyk.amocrm.ru/api/v4/contacts');
        $data = json_decode((string)$api, true);

        if(is_null($data)) {
            return back()->with('error', 'К сожалению, выгружать пока нечего.');
        }

        $contacts = $data['_embedded']['contacts'];

        foreach ($contacts as $contact) {
            Contact::query()->updateOrCreate([
                'amocrm_id' => $contact['id'],
                'name' => $contact['name'],
                'responsible_user_id' => ResponsibleUser::where('amocrm_id', $contact['responsible_user_id'])->pluck('id')[0],
                'account_id' => Account::where('amocrm_id', $contact['account_id'])->pluck('id')[0],
            ]);
        }
        return back()->with('success', 'Успешно');
    }
}
