<?php

namespace App\Bots\Telegram\Commands;

use App\Models\Group;
use App\Models\Subscription;
use Carbon\Carbon;

class GetToday extends AbstractCommand
{
    const COMMAND = '/today';

    public function run(int $stage = 0): void
    {
        /** @var Subscription|Group $group */
        $subscription = Subscription::query()
            ->where('chat_id', $this->update->message->chat->id)
            ->with('group')
            ->first();

        $day = $subscription->group->days()->where('date', Carbon::now()->toDateString())->latest('created_at')->first();

        $this->response(json_encode($day, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
