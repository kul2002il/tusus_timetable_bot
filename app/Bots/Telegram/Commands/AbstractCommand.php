<?php

namespace App\Bots\Telegram\Commands;

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

    abstract public function run(): void;

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
}