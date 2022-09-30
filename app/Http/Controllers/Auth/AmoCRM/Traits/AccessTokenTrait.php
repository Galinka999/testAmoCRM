<?php

namespace App\Http\Controllers\Auth\AmoCRM\Traits;

trait AccessTokenTrait
{
    public function getToken()
    {
        $data = \Storage::disk('local')->get('access_token.txt');
        $accessToken = json_decode($data, true);
        return new \League\OAuth2\Client\Token\AccessToken([
            'access_token' => $accessToken['accessToken'],
            'refresh_token' => $accessToken['refreshToken'],
            'expires' => $accessToken['expires'],
            'baseDomain' => $accessToken['baseDomain'],
        ]);
    }
}
