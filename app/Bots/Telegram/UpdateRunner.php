<?php

namespace App\Bots\Telegram;

use App\Bots\Telegram\Commands\GetToday;
use App\Bots\Telegram\Commands\Help;
use App\Bots\Telegram\Commands\Logic\AbstractCommand;
use App\Bots\Telegram\Commands\NotFound;
use App\Bots\Telegram\Commands\Ping;
use App\Bots\Telegram\Commands\Restart;
use App\Bots\Telegram\Commands\Start;
use App\Bots\Telegram\Commands\Subscribe;
use App\Models\Pipeline;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Luzrain\TelegramBotApi\BotApi;
use Luzrain\TelegramBotApi\Method\SendMessage;
use Luzrain\TelegramBotApi\Type\MessageEntity;
use Luzrain\TelegramBotApi\Type\Update;

class UpdateRunner
{
    public const COMMANDS = [
        Start::COMMAND     => Start::class,
        Help::COMMAND      => Help::class,
        GetToday::COMMAND  => GetToday::class,
        Subscribe::COMMAND => Subscribe::class,
        Ping::COMMAND      => Ping::class,
        Restart::COMMAND   => Restart::class,
    ];

    protected ?Update $update;

    public function run(Update $update): void
    {
        $this->update = $update;

        try {
            $this->resolvePipeline() ||
            $this->resolveCommand() ||
            $this->response('Неизвестно что с этим делать.');
        } catch (\Exception $e) {
            Log::error("Exception: {$e->getMessage()}\n{$e->getTraceAsString()}");
        }
    }

    private function resolvePipeline(): bool
    {
        /** @var Pipeline|null $pipeline */
        $pipeline = Pipeline::query()->where('chat_id', $this->update->message->chat->id)->first();

        if (!$pipeline) {
            return false;
        }

        $this->createCommandByName($pipeline->command)->run($pipeline->stage);

        return true;
    }

    private function resolveCommand(): bool
    {
        foreach ($this->update->message->entities ?? [] as $entity) {
            if ($entity instanceof MessageEntity && $entity->type === 'bot_command') {
                $commandName = substr($this->update->message->text, $entity->offset, $entity->length);
                $this->createCommandByName($commandName)->run();

                return true;
            }
        }

        return false;
    }

    private function createCommandByName(string $name): AbstractCommand
    {
        $command = self::COMMANDS[$name] ?? NotFound::class;

        return (new $command())->setUpdate($this->update);
    }

    private function response(string $text): void
    {
        App::make(BotApi::class)->call(new SendMessage(
            chatId: $this->update->message->chat->id,
            text: $text,
        ));
    }
}
