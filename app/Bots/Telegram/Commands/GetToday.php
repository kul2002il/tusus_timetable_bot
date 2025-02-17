<?php

namespace App\Bots\Telegram\Commands;

use App\Helpers\Parsers\TimetableParser;
use App\Models\Group;
use App\Models\Subscription;
use Carbon\Carbon;

class GetToday extends AbstractCommand
{
    public function run(): void
    {
        /** @var Subscription|Group $group */
        $subscription = Subscription::query()
            ->where('chat_id', $this->update->message->chat->id)
            ->with('group')
            ->first();

        $source = file_get_contents("https://timetable.tusur.ru/faculties/{$subscription->group->faculty}/groups/{$subscription->group->number}");

        $day = (new TimetableParser($source))->getTimetable()[Carbon::now()->toDateString()] ?? 'Не найдено';

        $this->response(json_encode($day, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
