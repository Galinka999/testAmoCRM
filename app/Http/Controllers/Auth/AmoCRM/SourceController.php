<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\AmoCRM;

use App\Http\Controllers\Auth\AmoCRM\Traits\AccessTokenTrait;
use App\Http\Controllers\Controller;
use App\Models\LeadPipeline;
use App\Models\Source;
use App\Services\AmoCrmService;

class SourceController extends Controller
{
    use AccessTokenTrait;

    public function getSources(AmoCrmService $amoCrmService): \Illuminate\Http\RedirectResponse
    {
        try {
            $accessToken = $this->getToken();

            $apiClient = $amoCrmService->getApiClient()->setAccessToken($accessToken);

            $sources = $apiClient->sources()->get()->toArray();

            foreach ($sources as $source) {

                $pipeline = LeadPipeline::where('amocrm_id', $source['pipeline_id'])->pluck('id');
                if($pipeline->isEmpty()) {
                    return back()->with('error', 'Выгрузите сначала данные по Воронкам');
                }
                $pipelineId = $pipeline[0];

                Source::query()->upsert([
                    'amocrm_id' => $source['id'],
                    'name' => $source['name'],
                    'pipeline_id' => $pipelineId,
                    'default' => $source['default'],
                    'external_id' =>$source['external_id'],
                ], ['amocrm_id'], ['name', 'pipeline_id', 'default', 'external_id']);
            }
            return back()->with('success', 'Успешно');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

    }
}
