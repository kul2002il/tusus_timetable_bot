<?php

namespace App\Bots\Telegram\Commands\Logic;

use Illuminate\Support\Facades\App;
use Luzrain\TelegramBotApi\BotApi;
use Luzrain\TelegramBotApi\Exception\TelegramApiException;
use Luzrain\TelegramBotApi\Method\SendMessage;
use Luzrain\TelegramBotApi\Type\Update;
use Psr\Http\Client\ClientExceptionInterface;

abstract class AbstractCommand
{
    protected BotApi $bot;

    protected ?Update $update;

    public function __construct()
    {
        $this->bot = App::make(BotApi::class);
    }

    public function setUpdate(Update $update): self
    {
        $this->update = $update;

        return $this;
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
}
