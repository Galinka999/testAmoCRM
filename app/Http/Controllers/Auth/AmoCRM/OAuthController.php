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
        $state = $_COOKIE['laravel_session'] ?: bin2hex(random_bytes(16));
//        $_SESSION['oauth2state'] = $state;
        $_COOKIE['laravel_session'] = $state;

        if($state) {
            echo $this->provider->getOAuthButton([
                'title' => 'Установить интеграцию',
                'compact' => false,
                'class_name' => 'className',
                'color' => 'green',
                'error_callback' => 'handleOauthError',
                'state' => $state,
            ]);
            echo "<br><a href='https://e676-90-188-56-101.ngrok.io'> Назад </a>";
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
//        return redirect()->route('welcome');
    }

}
