<?php

namespace App\Bots\Telegram;

use App\Jobs\RunBotCommand;
use Illuminate\Support\Facades\App;
use Luzrain\TelegramBotApi\BotApi;
use Luzrain\TelegramBotApi\Method\GetUpdates;
use Luzrain\TelegramBotApi\Type\Update;

class TelegramBot
{
    private BotApi $bot;
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
            $this->offset = $update->updateId + 1;

            if (!$update->message && !$update->callbackQuery) {
                continue;
            }

            RunBotCommand::dispatch($update)->onQueue('messages');
        }
    }
}
