<?php

declare(strict_types=1);

namespace App\Http\Controllers\AmoCRM;

use App\Http\Controllers\AmoCRM\Traits\AccessTokenTrait;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Contact;
use App\Models\ResponsibleUser;
use App\Services\AmoCrmService;

class ContactController extends Controller
{
    use AccessTokenTrait;

    public function getContacts(AmoCrmService $amoCrmService): \Illuminate\Http\RedirectResponse
    {
        try {
            $accessToken = $this->getToken();

            $apiClient = $amoCrmService->getApiClient()->setAccessToken($accessToken);

            $contacts = $apiClient->contacts()->get()->toArray();

            foreach ($contacts as $contact) {

                $account = Account::where('amocrm_id', $contact['account_id'])->pluck('id');
                if($account->isEmpty()) {
                    return back()->with('error', 'Выгрузите сначала данные по Аккаунту');
                }
                $accountId = $account[0];

                Contact::query()->upsert([
                    'amocrm_id' => $contact['id'],
                    'name' => $contact['name'],
                    'responsible_user_id' => ResponsibleUser::where('amocrm_id', $contact['responsible_user_id'])->pluck('id')[0],
                    'account_id' => $accountId,
                ], ['amocrm_id'], ['name', 'responsible_user_id', 'account_id']);
            }
            return back()->with('success', 'Успешно');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
