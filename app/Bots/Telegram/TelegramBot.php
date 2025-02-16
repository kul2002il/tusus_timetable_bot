<?php

namespace App\Bots\Telegram;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Luzrain\TelegramBotApi\BotApi;
use Luzrain\TelegramBotApi\Method\GetUpdates;
use Luzrain\TelegramBotApi\Method\SendMessage;
use Luzrain\TelegramBotApi\Type;

class TelegramBot
{
    public function run(): never
    {
        $bot = $this->createBot();

        $offset = 0;

        while(1) {
            /** @var Type\Update[] $response */
            $response = $bot->call(new GetUpdates(offset: $offset, timeout: 5));

            foreach ($response as $update) {
                $bot->call(new SendMessage(
                    chatId: $update->message->from->id,
                    text: 'pong',
                ));
                $offset = $update->updateId + 1;
            }
        }
    }

    private function createBot(): BotApi
    {
        $httpFactory = new HttpFactory();
        $httpClient = new Client(['http_errors' => false]);

        return new BotApi(
            requestFactory: $httpFactory,
            streamFactory: $httpFactory,
            client: $httpClient,
            token: config('telegram.api_key'),
        );
    }
}