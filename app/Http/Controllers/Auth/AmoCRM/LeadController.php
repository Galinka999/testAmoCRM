<?php

namespace App\Http\Controllers\Auth\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use App\Http\Controllers\Controller;
use App\Models\Token;
use App\Services\AmoCrmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LeadController extends Controller
{
    public function getLeads()
    {
        $token = Token::query()->latest()->first();
        $access_token = $token->access_token;

        $api = HTTP::withToken($access_token)->get('https://galina89ruzhyk.amocrm.ru/api/v4/leads');
        $array = json_decode($api, true);

        $leads = $array['_embedded']['leads'];
        dd($leads);

    }
}
