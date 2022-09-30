<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\AmoCRM;

use App\Http\Controllers\Controller;
use App\Models\LeadPipeline;
use App\Models\Source;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SourceController extends Controller
{
    public function getSources(): \Illuminate\Http\RedirectResponse
    {
        $token = Token::query()->latest()->first();
        $access_token = $token->access_token;

        $api = HTTP::withToken($access_token)->get('https://galina89ruzhyk.amocrm.ru/api/v4/sources');
        $data = json_decode((string)$api, true);

        if(is_null($data)) {
            return back()->with('error', 'К сожалению, выгружать пока нечего.');
        }

        $sources = $data['_embedded']['sources'];

        foreach ($sources as $source) {

            if(!LeadPipeline::where('amocrm_id', $source['pipeline_id'])->pluck('id')) {
                return back()->with('error', "Выгрузите сначала данные по Воронкам");
            }

            Source::query()->updateOrCreate([
                'amocrm_id' => $source['id'],
                'name' => $source['name'],
                'pipeline_id' => LeadPipeline::where('amocrm_id', $source['pipeline_id'])->pluck('id')[0],
                'default' => $source['default'],
                'external_id' =>$source['external_id'],
            ]);
        }
        return back()->with('success', 'Успешно');
    }
}
