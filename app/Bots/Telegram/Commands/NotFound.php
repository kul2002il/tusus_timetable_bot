<?php

namespace App\Bots\Telegram\Commands;

class NotFound extends AbstractCommand
{
    public function run(): void
    {
        $this->response('Неизвестная команда.');
    }
}
