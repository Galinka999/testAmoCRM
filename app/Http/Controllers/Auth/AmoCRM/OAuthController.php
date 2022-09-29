<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\AmoCRM;

use AmoCRM\Exceptions\BadTypeException;
use App\Http\Controllers\Controller;
use App\Models\Token;
use App\Services\AmoCrmService;

class OAuthController extends Controller
{
    public function __construct(AmoCrmService $amoCrmService)
    {
        $this->provider = $amoCrmService->getProvider();
    }

    /**
     * @throws BadTypeException
     */
    public function redirect()
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth2state'] = $state;

        if($state) {
            echo $this->provider->getOAuthButton([
                'title' => 'Установить coединение',
                'compact' => false,
                'class_name' => 'className',
                'color' => 'green',
                'error_callback' => 'handleOauthError',
                'state' => $state,
            ]);
        } elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }
    }

    public function callback()
    {
        try {
            $provider = $this->provider;
            $accessToken = $this->provider->getAccessTokenByCode($_GET['code']);

            if (!$accessToken->hasExpired()) {
                $token = Token::query()->create([
                    'access_token' => $accessToken->getToken(),
                    'refresh_token' => $accessToken->getRefreshToken(),
                    'provider' => 'amoCRM',
                ]);
            }

        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        $ownerDetails = $provider->getResourceOwner($accessToken);

        dd('Hello, ' . $ownerDetails->getName() . '!' );
    }

}
