<?php

namespace App\Bots\Telegram\Commands;

use App\Bots\Telegram\Commands\Logic\AbstractCommand;

class NotFound extends AbstractCommand
{
    public function run(int $stage = 0): void
    {
        $this->response('Неизвестная команда.');
    }
}
