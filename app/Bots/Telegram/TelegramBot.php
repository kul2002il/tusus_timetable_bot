<?php

namespace App\Bots\Telegram;

use App\Bots\Telegram\Commands\AbstractCommand;
use App\Bots\Telegram\Commands\GetToday;
use App\Bots\Telegram\Commands\NotFound;
use App\Bots\Telegram\Commands\Ping;
use App\Bots\Telegram\Commands\Start;
use App\Bots\Telegram\Commands\Subscribe;
use App\Models\Pipeline;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Luzrain\TelegramBotApi\BotApi;
use Luzrain\TelegramBotApi\Method\GetUpdates;
use Luzrain\TelegramBotApi\Method\SendMessage;
use Luzrain\TelegramBotApi\Type\MessageEntity;
use Luzrain\TelegramBotApi\Type\Update;

class TelegramBot
{
    const COMMANDS = [
        Start::COMMAND    => Start::class,
        Ping::COMMAND     => Ping::class,
        GetToday::COMMAND => GetToday::class,
        Subscribe::COMMAND=> Subscribe::class,
    ];

    private BotApi $bot;
    private ?Update $currentUpdate = null;
    private int $offset = 0;

    public function __construct()
    {
        $this->bot = App::make(BotApi::class);
    }

    public function iteration(): void
    {
        /** @var Update[] $response */
        $response = $this->bot->call(new GetUpdates(offset: $this->offset, limit: 10, timeout: 40));

        foreach ($response as $update) {
            $this->currentUpdate = $update;
            $this->offset = $update->updateId + 1;

            if (!$this->currentUpdate->message) {
                continue;
            }

            try {
                $this->resolvePipeline() ||
                $this->resolveCommand() ||
                $this->response('Неизвестно что с этим делать.');
            } catch (\Exception $e) {
                Log::error("Exception: {$e->getMessage()}\n{$e->getTraceAsString()}");
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
        return new $command($this->currentUpdate);
    }

    private function response(string $text): void
    {
        $this->bot->call(new SendMessage(
            chatId: $this->currentUpdate->message->chat->id,
            text: $text,
        ));
    }
}