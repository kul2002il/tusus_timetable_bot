<?php

namespace App\Bots\Telegram\Commands;

class NotFound extends AbstractCommand
{
    public function run(int $stage = 0): void
    {
        $this->response('Неизвестная команда.');
    }
}
