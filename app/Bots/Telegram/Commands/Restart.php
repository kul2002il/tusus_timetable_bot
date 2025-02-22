<?php

namespace App\Bots\Telegram\Commands;

use Illuminate\Support\Facades\Cache;
use Luzrain\TelegramBotApi\Method\GetUpdates;

class Restart extends AbstractCommand
{
    public const COMMAND = '/restart';

    public function run(int $stage = 0): void
    {
        if ($this->update->message->chat->id !== (int) config('telegram.admin_chat_id')) {
            $this->response('Недостаточно прав.');

            return;
        }

        // Mark /restart as completed.
        $this->bot->call(new GetUpdates(offset: $this->update->updateId + 1, limit: 1, timeout: 0));

        Cache::delete('telegram_bot_daemon_id');
        $this->response('Кеш сброшен, бот в течении двух минут перезагрузится.');
    }
}
