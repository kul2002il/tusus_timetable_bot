<?php

namespace App\Bots\Telegram;

use App\Bots\Telegram\Commands\AbstractCommand;
use App\Bots\Telegram\Commands\NotFound;
use App\Bots\Telegram\Commands\Ping;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Luzrain\TelegramBotApi\BotApi;
use Luzrain\TelegramBotApi\Method\GetUpdates;
use Luzrain\TelegramBotApi\Type\MessageEntity;
use Luzrain\TelegramBotApi\Type\Update;

class TelegramBot
{
    const COMMANDS = [
        '/ping' => Ping::class,
    ];

    private BotApi $bot;

    public function __construct()
    {
        $this->bot = $this->createBot();
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

    public function run(): never
    {
        $offset = 0;

        while(1) {
            /** @var Update[] $response */
            $response = $this->bot->call(new GetUpdates(offset: $offset, timeout: 50));

            foreach ($response as $update) {
                $this->resolveCommand($update);

                $offset = $update->updateId + 1;
            }
        }
    }

    private function resolveCommand(Update $update): void
    {
        foreach ($update->message->entities as $entity) {
            if ($entity instanceof MessageEntity && $entity->type === 'bot_command') {
                $commandName = substr($update->message->text, $entity->offset, $entity->length);
                /** @var AbstractCommand $command */
                $command = self::COMMANDS[$commandName] ?? NotFound::class;
                (new $command($this->bot, $update))->run();
            }
        }
    }
}