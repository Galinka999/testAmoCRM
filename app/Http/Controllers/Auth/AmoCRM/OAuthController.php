<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\AmoCRM;

use AmoCRM\Exceptions\BadTypeException;
use App\Http\Controllers\Controller;
use App\Models\Token;
use App\Services\AmoCrmService;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use OAuthService;

class OAuthController extends Controller
{
    public function __construct(AmoCrmService $amoCrmService)
    {
        $this->provider = $amoCrmService->getProvider();
        $this->apiClient = $amoCrmService->getApiClient();
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

    public function callback(AmoCrmService $amoCrmService)
    {
        try {
            $accessToken = $this->provider->getAccessTokenByCode($_GET['code']);

            if (!$accessToken->hasExpired()) {
//                $token = Token::query()->create([
//                    'access_token' => $accessToken->getToken(),
//                    'refresh_token' => $accessToken->getRefreshToken(),
//                    'provider' => 'amoCRM',
//                ]);
                $data = [
                    'accessToken' => $accessToken->getToken(),
                    'expires' => $accessToken->getExpires(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'baseDomain' => config('services.amocrm.client_account'),
                ];

                \Storage::disk('local')->put('access_token.txt', json_encode($data));
//                    file_put_contents(TOKEN_FILE, json_encode($data));
            } else {
                exit('Invalid access token ' . var_export($accessToken, true));
            }

            $this->apiClient->setAccessToken($accessToken);

        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        $ownerDetails = $this->provider->getResourceOwner($accessToken);

        dd('Hello, ' . $ownerDetails->getName() . '!' );
    }

}
