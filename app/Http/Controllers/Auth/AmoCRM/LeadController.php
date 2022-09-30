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

            if($lead['source_id']) {
                $source = Source::where('amocrm_id', $lead['source_id'])->pluck('id');
                if($source->isEmpty()) {
                    return back()->with(['error', 'Выгрузите сначала данные по Источникам']);
                }
                $sourceId = $source[0];
            } else {
                $sourceId = null;
            }

            if($lead['updated_by']) {
                $updatedBy = ResponsibleUser::where('amocrm_id', $lead['updated_by'])->pluck('id');
                if($updatedBy->isEmpty()) {
                    return back()->with(['error', 'Выгрузите сначала данные по Пользователям']);
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

            $updated_at = (new \DateTime)->setTimestamp ($lead['updated_at']) ?: null;


            Lead::query()->updateOrCreate([
                'amocrm_id' => $lead['id'],
                'name' => $lead['name'],
                'price' => $lead['price'],
                'responsible_user_id' => ResponsibleUser::where('amocrm_id', $lead['responsible_user_id'])->pluck('id')[0],
                'status_id' => LeadStatus::where('amocrm_id', $lead['status_id'])->pluck('id')[0],
                'loss_reason_id' => $lossReasonNewId,
                'pipeline_id' => LeadPipeline::where('amocrm_id', $lead['pipeline_id'])->pluck('id')[0],
                'created_by' => ResponsibleUser::where('amocrm_id', $lead['created_by'])->pluck('id')[0],
                'updated_by' => $updatedById,
                'created_at' => (new \DateTime)->setTimestamp ($lead['created_at']),
                'updated_at' => $updated_at,
                'closed_at' => $closedAt,
                'closest_task_at' => $closestTaskAt,
                'is_deleted' => $lead['is_deleted'],
                'custom_fields_values' => $lead['custom_fields_values'],
                'score' => $lead['score'],
                'account_id' => Account::where('amocrm_id', $lead['account_id'])->pluck('id')[0],
                'source_id' =>  $sourceId,
                'is_price_modified_by_robot' => $lead['is_price_modified_by_robot'],
            ]);
        }

    }

}
