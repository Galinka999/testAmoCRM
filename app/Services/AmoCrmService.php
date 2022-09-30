<?php

declare(strict_types=1);

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\OAuth\AmoCRMOAuth;
use League\OAuth2\Client\Token\AccessToken;

class AmoCrmService
{
    public function getProvider(): AmoCRMOAuth
    {
        $provider = new AmoCRMOAuth(
            config('services.amocrm.client_id'),
            config('services.amocrm.client_secret'),
            config('services.amocrm.client_redirect_uri')
        );

        $provider->setBaseDomain(config('services.amocrm.client_account'));

        return $provider;
    }

    public function getApiClient(): AmoCRMApiClient
    {
        $apiClient = new AmoCRMApiClient(
            config('services.amocrm.client_id'),
            config('services.amocrm.client_secret'),
            config('services.amocrm.client_redirect_uri')
        );
        $apiClient->setAccountBaseDomain(config('services.amocrm.client_account'));
        return $apiClient;
    }

}
