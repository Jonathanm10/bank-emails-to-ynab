<?php

namespace App\Providers;

use App\Exceptions\UnableToAuthenticateException;
use Google_Client;
use Google_Service_Gmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->when(Google_Service_Gmail::class)
            ->needs('$clientOrConfig')
            ->give(function () {
                $client = new Google_Client();

                $client->setAuthConfig(Storage::path('credentials.json'));
                $client->setScopes([Google_Service_Gmail::GMAIL_READONLY, Google_Service_Gmail::GMAIL_MODIFY]);
                $client->setAccessType('offline');
                $client->setPrompt('select_account consent');

                if ($accessToken = Storage::get('token.json')) {
                    $accessToken = json_decode($accessToken, true, 512, JSON_THROW_ON_ERROR);
                    $client->setAccessToken($accessToken);
                }

                if ($client->isAccessTokenExpired()) {
                    if ($refreshToken = $accessToken['refresh_token'] ?? null) {
                        $accessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                        Storage::put('token.json', json_encode($accessToken, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
                    } else {
                        throw UnableToAuthenticateException::because('Unable to get refresh token.');
                    }
                }

                return $client;
            });
    }
}
