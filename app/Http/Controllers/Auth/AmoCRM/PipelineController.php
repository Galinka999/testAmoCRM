<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\AmoCRM;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\LeadPipeline;
use App\Models\LeadStatus;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PipelineController extends Controller
{
    public function getPipelines(): \Illuminate\Http\RedirectResponse
    {
        $token = Token::query()->latest()->first();
        $access_token = $token->access_token;

        $api = HTTP::withToken($access_token)->get('https://galina89ruzhyk.amocrm.ru/api/v4/leads/pipelines');
        $data = json_decode((string)$api, true);

        if(is_null($data)) {
            return back()->with('error', 'К сожалению, выгружать пока нечего.');
        }

        $pipelines = $data['_embedded']['pipelines'];

        foreach ($pipelines as $pipeline) {

            $account = Account::where('amocrm_id', $pipeline['account_id'])->pluck('id');
            if($account->isEmpty()) {
                return back()->with('error', 'Выгрузите сначала данные по Аккаунту');
            }
            $accountId = $account[0];

            $pipeline_new = LeadPipeline::query()->updateOrCreate([
                'amocrm_id' => $pipeline['id'],
                'name' => $pipeline['name'],
                'is_main' => $pipeline['is_main'],
                'is_archive' => $pipeline['is_archive'],
                'account_id' => $accountId,
            ]);

            $statuses = $pipeline['_embedded']['statuses'];

            foreach ($statuses as $status) {
                LeadStatus::query()->updateOrCreate([
                    'amocrm_id' => $status['id'],
                    'name' => $status['name'],
                    'sort' => $status['sort'],
                    'pipeline_id' => $pipeline_new->id,
                    'account_id' => $pipeline_new->account_id,
                ]);
            }
        }
        return back()->with('success', 'Успешно');
    }
}
