<?php

namespace App\Bots\Telegram\Commands;

use App\Bots\Telegram\Commands\Logic\AbstractCommand;

class Ping extends AbstractCommand
{
    public const COMMAND = '/ping';

    public function run(int $stage = 0): void
    {
        $this->response('pong ' . $this->update->message->chat->id);
    }
}
