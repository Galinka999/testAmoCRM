<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\AmoCRM;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\LeadPipeline;
use App\Models\LeadStatus;
use App\Models\LossReason;
use App\Models\ResponsibleUser;
use App\Models\Source;
use App\Models\Token;
use DateTime;
use Illuminate\Support\Facades\Http;

class LeadController extends Controller
{
    public function getLeads()
    {
        $token = Token::query()->latest()->first();
        $access_token = $token->access_token;

        $api = HTTP::withToken($access_token)->get('https://galina89ruzhyk.amocrm.ru/api/v4/leads?with=catalog_elements,loss_reason,contacts,source_id,is_price_modified_by_robot');
        $data = json_decode((string)$api, true);

        if(is_null($data)) {
            return back()->with('error', 'К сожалению, выгружать пока нечего.');
        }

        $leads = $data['_embedded']['leads'];

        foreach ($leads as $lead) {

            $lossReasonNewId = $this->getLossReason($lead);

            if($lead['source_id']) {
                $source = Source::where('amocrm_id', $lead['source_id'])->pluck('id');
                if($source->isEmpty()) {
                    return back()->with('error', 'Выгрузите сначала данные по Источникам');
                }
                $sourceId = $source[0];
            } else {
                $sourceId = null;
            }

            $responsibleUser = ResponsibleUser::where('amocrm_id', $lead['responsible_user_id'])->pluck('id');
            if($responsibleUser->isEmpty()) {
                return back()->with('error', 'Выгрузите сначала данные по Пользователям');
            }
            $responsibleUserId = $responsibleUser[0];

            $createdBy = ResponsibleUser::where('amocrm_id', $lead['created_by'])->pluck('id');
            if($createdBy->isEmpty()) {
                return back()->with('error', 'Выгрузите сначала данные по Пользователям');
            }
            $createdById = $createdBy[0];

            if($lead['updated_by']) {
                $updatedBy = ResponsibleUser::where('amocrm_id', $lead['updated_by'])->pluck('id');
                if($updatedBy->isEmpty()) {
                    return back()->with('error', 'Выгрузите сначала данные по Пользователям');
                }
                $updatedById = $updatedBy[0];
            } else {
                $updatedById = null;
            }

            if($lead['closed_at']) {
                $closedAt = (new \DateTime)->setTimestamp ($lead['closed_at']);
            } else {
                $closedAt = null;
            }

            if($lead['closest_task_at']) {
                $closestTaskAt = (new \DateTime)->setTimestamp ($lead['closest_task_at']);
            } else {
                $closestTaskAt = null;
            }

            if($lead['updated_at']) {
                $updatedAt = (new \DateTime)->setTimestamp ($lead['updated_at']);
            } else {
                $updatedAt = null;
            }

            $status = LeadStatus::where('amocrm_id', $lead['status_id'])->pluck('id');
            if($status->isEmpty()) {
                return back()->with('error', 'Выгрузите сначала данные по Статусам');
            }
            $statusId = $status[0];

            $pipeline = LeadPipeline::where('amocrm_id', $lead['pipeline_id'])->pluck('id');
            if($pipeline->isEmpty()) {
                return back()->with('error', 'Выгрузите сначала данные по Воронкам');
            }
            $pipelineId = $pipeline[0];

            $account = Account::where('amocrm_id', $lead['account_id'])->pluck('id');
            if($account->isEmpty()) {
                return back()->with('error', 'Выгрузите сначала данные по Аккаунту');
            }
            $accountId = $account[0];

            Lead::query()->upsert([
                'amocrm_id' => $lead['id'],
                'name' => $lead['name'],
                'price' => $lead['price'],
                'responsible_user_id' => $responsibleUserId,
                'status_id' => $statusId,
                'loss_reason_id' => $lossReasonNewId,
                'pipeline_id' => $pipelineId,
                'created_by' => $createdById,
                'updated_by' => $updatedById,
                'created_at' => (new \DateTime)->setTimestamp ($lead['created_at']),
                'updated_at' => $updatedAt,
                'closed_at' => $closedAt,
                'closest_task_at' => $closestTaskAt,
                'is_deleted' => $lead['is_deleted'],
                'custom_fields_values' => $lead['custom_fields_values'],
                'score' => $lead['score'],
                'account_id' => $accountId,
                'source_id' =>  $sourceId,
                'is_price_modified_by_robot' => $lead['is_price_modified_by_robot'],
            ], ['amocrm_id'], ['name', 'price', 'responsible_user_id', 'status_id', 'loss_reason_id',
                'pipeline_id', 'created_by', 'updated_by', 'closed_at', 'closest_task_at',
                'is_deleted', 'custom_fields_values', 'score', 'account_id', 'source_id',
                'is_price_modified_by_robot']);
        }

        return back()->with('success', 'Успешно');
    }

    public function getLossReason(array $lead)
    {
        if(array_key_exists(0,  $lead['_embedded']['loss_reason'])) {
            $lossReason = $lead['_embedded']['loss_reason'][0];

            $lossReasonNew = LossReason::query()->updateOrCreate([
                'amocrm_id' => $lossReason['id'],
                'name' => $lossReason['name'],
                'sort' => $lossReason['sort'],
            ]);
            $lossReasonNewId = $lossReasonNew->id;
        } else {
            $lossReasonNewId = null;
        }
        return $lossReasonNewId;
    }

}
