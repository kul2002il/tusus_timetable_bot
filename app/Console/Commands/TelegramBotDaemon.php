<?php

namespace App\Console\Commands;

use App\Bots\Telegram\TelegramBot;
use Illuminate\Console\Command;

class TelegramBotDaemon extends Command
{
    protected $signature = 'telegram:run';

    protected $description = 'Run telegram bot.';

    public function handle()
    {
        (new TelegramBot())->run();
    }
}
