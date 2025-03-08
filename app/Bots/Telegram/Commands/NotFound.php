<?php

namespace App\Bots\Telegram\Commands;

use App\Bots\Telegram\Commands\Logic\AbstractCommand;
use App\Bots\Telegram\Commands\Logic\ButtonCallbackExecutable;
use App\Bots\Telegram\Commands\Logic\HasButtons;

class NotFound extends AbstractCommand implements ButtonCallbackExecutable
{
    use HasButtons;

    public function getCommand(): string
    {
        return '';
    }

    public function runButton(string $callback): void
    {
        $this->response("Сломанная кнопка с данными $callback");
    }

    public function run(int $stage = 0): void
    {
        $this->response('Неизвестная команда.');
    }
}
