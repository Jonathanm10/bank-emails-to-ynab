<?php

namespace App\Commands;

use Google\Exception;
use Google_Client;
use Google_Service_Gmail;
use Illuminate\Support\Facades\Storage;
use JsonException;
use LaravelZero\Framework\Commands\Command;

class GenerateTokenJsonCommand extends Command
{
    protected $signature = 'generate-token';

    protected $description = 'Build url to validate the application and generate an access/refresh token.';

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function handle(Google_Client $client): int
    {
        $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
        $client->setAuthConfig(Storage::path('credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        $authUrl = $client->createAuthUrl();
        $this->info('Open the following link in your browser:' . PHP_EOL . $authUrl);
        $authCode = $this->ask('Enter verification code');
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        if (array_key_exists('error', $accessToken)) {
            throw new Exception(implode(', ', $accessToken));
        }

        $client->setAccessToken($accessToken);

        Storage::put('token.json', json_encode($accessToken, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
