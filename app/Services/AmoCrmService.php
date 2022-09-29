<?php

declare(strict_types=1);

namespace App\Services;

use AmoCRM\OAuth\AmoCRMOAuth;

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

}
