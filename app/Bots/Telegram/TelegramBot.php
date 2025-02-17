<?php

namespace App\Bots\Telegram;

use App\Bots\Telegram\Commands\AbstractCommand;
use App\Bots\Telegram\Commands\GetToday;
use App\Bots\Telegram\Commands\NotFound;
use App\Bots\Telegram\Commands\Ping;
use App\Bots\Telegram\Commands\Subscribe;
use App\Models\Pipeline;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Luzrain\TelegramBotApi\BotApi;
use Luzrain\TelegramBotApi\Method\GetUpdates;
use Luzrain\TelegramBotApi\Method\SendMessage;
use Luzrain\TelegramBotApi\Type\MessageEntity;
use Luzrain\TelegramBotApi\Type\Update;

class TelegramBot
{
    const COMMANDS = [
        Ping::COMMAND     => Ping::class,
        GetToday::COMMAND => GetToday::class,
        Subscribe::COMMAND=> Subscribe::class,
    ];

    private BotApi $bot;
    private ?Update $currentUpdate = null;

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
                $this->currentUpdate = $update;
                $offset = $update->updateId + 1;

                if (!$this->currentUpdate->message) {
                    continue;
                }

                $this->resolvePipeline() ||
                $this->resolveCommand() ||
                $this->response('Неизвестно что с этим делать.');
            }
        }
    }

    private function resolvePipeline(): bool
    {
        /** @var Pipeline|null $pipeline */
        $pipeline = Pipeline::query()->where('chat_id', $this->currentUpdate->message->chat->id)->first();

        if (!$pipeline) {
            return false;
        }

        $this->createCommandByName($pipeline->command)->run($pipeline->stage);

        return true;
    }

    private function resolveCommand(): bool
    {
        foreach ($this->currentUpdate->message->entities ?? [] as $entity) {
            if ($entity instanceof MessageEntity && $entity->type === 'bot_command') {
                $commandName = substr($this->currentUpdate->message->text, $entity->offset, $entity->length);
                $this->createCommandByName($commandName)->run();
                return true;
            }
        }
        return false;
    }

    private function createCommandByName(string $name): AbstractCommand {
        $command = self::COMMANDS[$name] ?? NotFound::class;
        return new $command($this->bot, $this->currentUpdate);
    }

    private function response(string $text): void
    {
        $this->bot->call(new SendMessage(
            chatId: $this->currentUpdate->message->chat->id,
            text: $text,
        ));
    }
}