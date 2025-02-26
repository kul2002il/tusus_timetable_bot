<?php

namespace App\Bots\Telegram\Commands;

use App\Bots\Telegram\Commands\Logic\AbstractCommand;

class Start extends AbstractCommand
{
    public const COMMAND = '/start';

    public function run(int $stage = 0): void
    {
        $this->response(view('bot.start'));
    }
}
