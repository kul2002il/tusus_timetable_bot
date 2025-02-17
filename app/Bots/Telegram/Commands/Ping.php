<?php

namespace App\Bots\Telegram\Commands;

class Ping extends AbstractCommand
{
    public function run(): void
    {
        $this->response('pong ' . $this->update->message->chat->id);
    }
}
