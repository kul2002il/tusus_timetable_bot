<?php

namespace App\Bots\Telegram;

use App\Bots\Telegram\Commands\GetToday;
use App\Bots\Telegram\Commands\Help;
use App\Bots\Telegram\Commands\Logic\AbstractCommand;
use App\Bots\Telegram\Commands\Logic\ButtonCallbackExecutable;
use App\Bots\Telegram\Commands\NotFound;
use App\Bots\Telegram\Commands\Ping;
use App\Bots\Telegram\Commands\Restart;
use App\Bots\Telegram\Commands\Start;
use App\Bots\Telegram\Commands\Subscribe;
use App\Bots\Telegram\Commands\Timetable;
use App\Exceptions\Presentable;
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
        Timetable::COMMAND => Timetable::class,
    ];

    protected ?Update $update;

    public function run(Update $update): void
    {
        $this->update = $update;

        try {
            $this->resolvePipeline() ||
            $this->resolveCommand() ||
            $this->resolveButton() ||
            $this->response('Неизвестно что с этим делать.');
        } catch (Presentable $exception) {
            $this->response($exception->getMessage());
        } catch (\Exception $e) {
            Log::error("Exception: {$e->getMessage()}\n{$e->getTraceAsString()}");
        }
    }

    private function resolvePipeline(): bool
    {
        /** @var Pipeline|null $pipeline */
        $pipeline = Pipeline::query()->where('chat_id', $this->getChatId())->first();

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

    private function resolveButton(): bool
    {
        $query = $this->update->callbackQuery->data ?? '';

        if (!$query) {
            return false;
        }

        $query = explode(' ', $query, 2);

        $command = $this->createCommandByName($query[0]);

        if (!($command instanceof ButtonCallbackExecutable)) {
            return false;
        }

        $command->runButton($query[1]);

        return true;
    }

    private function createCommandByName(string $name): AbstractCommand
    {
        $command = self::COMMANDS[$name] ?? NotFound::class;

        return (new $command())->setUpdate($this->update);
    }

    private function response(string $text): void
    {
        App::make(BotApi::class)->call(new SendMessage(
            chatId: $this->getChatId(),
            text: $text,
        ));
    }

    private function getChatId(): int
    {
        return ($this->update->message ?? $this->update->callbackQuery->message)->chat->id;
    }
}
