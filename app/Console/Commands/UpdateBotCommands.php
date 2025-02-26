<?php

namespace App\Console\Commands;

use App\Bots\Telegram\Commands\Logic\Publicable;
use App\Bots\Telegram\UpdateRunner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Luzrain\TelegramBotApi\BotApi;
use Luzrain\TelegramBotApi\Method\SetMyCommands;
use Luzrain\TelegramBotApi\Type\BotCommand;

class UpdateBotCommands extends Command
{
    protected $signature = 'telegram:update-bot-commands';

    protected $description = 'Set commands to bot.';

    public function handle()
    {
        /** @var BotApi $bot */
        $bot = App::make(BotApi::class);

        $info = [];

        foreach (UpdateRunner::COMMANDS as $commandClass) {
            $command = new $commandClass();

            if ($command instanceof Publicable) {
                $info[] = new BotCommand($command->getCommand(), $command->getDescription());
            }
        }

        $bot->call(new SetMyCommands(commands: $info));
    }
}
