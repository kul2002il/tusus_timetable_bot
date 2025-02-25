<?php

namespace App\Console\Commands;

use App\Bots\Telegram\TelegramBot;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Cludge for run bot in cron instead of supervisor.
 */
class TelegramBotDaemon extends Command
{
    protected $signature = 'telegram:run';

    protected $description = 'Run telegram bot.';

    public function handle()
    {
        $id = Cache::get('telegram_bot_daemon_id');

        // Other bot is running.
        if ($id) {
            return;
        }

        Log::warning("Bot started.");

        $bot = new TelegramBot();

        $id = rand(1, 10000000);

        Cache::put('telegram_bot_daemon_id', $id, Carbon::now()->addSeconds(50));

        while (Cache::get('telegram_bot_daemon_id') === $id) {
            Cache::put('telegram_bot_daemon_id', $id, Carbon::now()->addSeconds(50));

            $bot->iteration();
        }

        Log::warning("Bot stopped.");
    }
}
