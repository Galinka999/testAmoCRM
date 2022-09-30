<?php

declare(strict_types=1);

namespace App\Http\Controllers\AmoCRM;

use App\Http\Controllers\AmoCRM\Traits\AccessTokenTrait;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\LeadPipeline;
use App\Models\LeadStatus;
use App\Services\AmoCrmService;

class PipelineController extends Controller
{
    use AccessTokenTrait;

    public function getPipelines(AmoCrmService $amoCrmService): \Illuminate\Http\RedirectResponse
    {
        try {
            $accessToken = $this->getToken();

            $apiClient = $amoCrmService->getApiClient()->setAccessToken($accessToken);

            $pipelines = $apiClient->pipelines()->get()->toArray();

            foreach ($pipelines as $pipeline) {

                $account = Account::where('amocrm_id', $pipeline['account_id'])->pluck('id');
                if ($account->isEmpty()) {
                    return back()->with('error', 'Выгрузите сначала данные по Аккаунту');
                }
                $accountId = $account[0];

                LeadPipeline::query()->upsert([
                    'amocrm_id' => $pipeline['id'],
                    'name' => $pipeline['name'],
                    'is_main' => $pipeline['is_main'],
                    'is_archive' => $pipeline['is_archive'],
                    'account_id' => $accountId,
                ], ['amocrm_id'], ['name', 'is_main', 'is_archive', 'account_id']);

                $statuses = $pipeline['statuses'];
                $pipeline_new = LeadPipeline::where('amocrm_id', $pipeline['id'])->first();

                foreach ($statuses as $status) {
                    LeadStatus::query()->upsert([
                        'amocrm_id' => $status['id'],
                        'name' => $status['name'],
                        'sort' => $status['sort'],
                        'pipeline_id' => $pipeline_new->id,
                        'account_id' => $pipeline_new->account_id,
                    ], ['amocrm_id'], ['name', 'sort', 'pipeline_id', 'account_id']);
                }
            }
            return back()->with('success', 'Успешно');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
