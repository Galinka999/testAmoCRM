<?php

declare(strict_types=1);

namespace App\Http\Controllers\AmoCRM;

use App\Http\Controllers\AmoCRM\Traits\AccessTokenTrait;
use App\Http\Controllers\Controller;
use App\Models\ResponsibleUser;
use App\Services\AmoCrmService;

class UserController extends Controller
{
    use AccessTokenTrait;

    public function getUsers(AmoCrmService $amoCrmService): \Illuminate\Http\RedirectResponse
    {
        try {
            $accessToken = $this->getToken();

            $apiClient = $amoCrmService->getApiClient()->setAccessToken($accessToken);

            $users = $apiClient->users()->get()->toArray();

            foreach ($users as $user) {
                ResponsibleUser::query()->upsert([
                    'amocrm_id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                ], ['amocrm_id'], ['name', 'email']);
            }
            return back()->with('success', 'Успешно');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
