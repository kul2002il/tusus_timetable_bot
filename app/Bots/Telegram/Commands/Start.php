<?php

namespace App\Bots\Telegram\Commands;

class Start extends AbstractCommand
{
    const COMMAND = '/start';

    public function run(int $stage = 0): void
    {
        $this->response(view('bot.start'));
    }
}