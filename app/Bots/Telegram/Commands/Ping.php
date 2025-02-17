<?php

namespace App\Bots\Telegram\Commands;

class Ping extends AbstractCommand
{
    const COMMAND = '/ping';

    public function run(int $stage = 0): void
    {
        $this->response('pong ' . $this->update->message->chat->id);
    }
}
