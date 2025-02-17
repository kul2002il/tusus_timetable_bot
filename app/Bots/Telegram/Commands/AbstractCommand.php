<?php

namespace App\Bots\Telegram\Commands;

use App\Models\Pipeline;
use Luzrain\TelegramBotApi\BotApi;
use Luzrain\TelegramBotApi\Exception\TelegramApiException;
use Luzrain\TelegramBotApi\Method\SendMessage;
use Luzrain\TelegramBotApi\Type\Update;
use Psr\Http\Client\ClientExceptionInterface;

abstract class AbstractCommand
{
    public function __construct(
        protected BotApi $bot,
        protected Update $update
    ) {
    }

    abstract public function run(int $stage = 0): void;

    /**
     * @throws TelegramApiException
     * @throws ClientExceptionInterface
     */
    protected function response(string $text): void
    {
        $this->bot->call(new SendMessage(
            chatId: $this->update->message->chat->id,
            text: $text,
        ));
    }

    protected function setPipelineState(int $state): void
    {
        $command = $this->getCommandName();
        if (!$command) {
            throw new \Exception('Can\'t set pipeline for command without name.');
        }
        Pipeline::query()->updateOrCreate(
            ['chat_id' => $this->update->message->chat->id],
            ['command' => $command, 'stage' => $state],
        );
    }

    protected function endPipeline(): void
    {
        Pipeline::query()->where('chat_id', $this->update->message->chat->id)->delete();
    }

    private function getCommandName(): ?string
    {
        return static::COMMAND ?? null;
    }
}