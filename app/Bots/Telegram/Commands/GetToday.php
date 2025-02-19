<?php

namespace App\Bots\Telegram\Commands;

use App\Models\Day;
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

        /** @var Day $day */
        $day = $subscription->group->days()->where('date', Carbon::now()->toDateString())->latest('created_at')->first();

        if (!$day) {
            $this->response('В настоящее время расписание ещё не загружено (обновление каждые 15 минут), либо пар нет.');
            return;
        }

        $this->response(
            view('bot.day', [
                'lessons' => json_decode($day->body),
            ])
        );
    }
}
