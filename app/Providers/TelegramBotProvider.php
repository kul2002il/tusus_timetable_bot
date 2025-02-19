<?php

namespace App\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Luzrain\TelegramBotApi\BotApi;

class TelegramBotProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BotApi::class, function (Application $app) {
            $httpFactory = new HttpFactory();

            return new BotApi(
                requestFactory: $httpFactory,
                streamFactory: $httpFactory,
                client: new Client(['http_errors' => false]),
                token: config('telegram.api_key'),
            );
        });
    }
}
