<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Luzrain\TelegramBotApi\BotApi;
use Luzrain\TelegramBotApi\Method\SendMessage;

class BotSendAll extends Command
{
    protected $signature = 'telegram:sending';

    protected $description = 'Sending to all subscribers.';

    public function handle()
    {
        /** @var BotApi $bot */
        $bot = App::make(BotApi::class);

        Subscription::query()->chunk(50, function (Collection $collection) use ($bot) {
            /** @var Subscription $subscription */
            foreach ($collection as $subscription) {
                try {
                    $bot->call(new SendMessage(chatId: $subscription->chat_id, text: view('bot.sending')));
                } catch (\Exception $e) {
                    Log::error("Exception: {$e->getMessage()}\n{$e->getTraceAsString()}");
                }
            }
        });
    }
}
