<?php

namespace App\Jobs;

use App\Bots\Telegram\UpdateRunner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Luzrain\TelegramBotApi\Type\Update;

class RunBotCommand implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(public Update $update)
    {
    }

    public function handle(): void
    {
        (new UpdateRunner())->run($this->update);
    }
}
