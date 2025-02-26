<?php

namespace App\Bots\Telegram\Commands;

use App\Bots\Telegram\Commands\Logic\AbstractCommand;
use App\Bots\Telegram\Commands\Logic\Publicable;
use App\Bots\Telegram\Commands\Logic\Published;

class Help extends AbstractCommand implements Publicable
{
    use Published;

    public const COMMAND = '/help';
    public const DESCRIPTION = 'Простая справка';

    public function run(int $stage = 0): void
    {
        $this->response(view('bot.help'));
    }
}
