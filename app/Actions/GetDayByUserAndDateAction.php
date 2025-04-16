<?php

namespace App\Actions;

use App\Exceptions\UserException;
use App\Models\Day;
use App\Models\Group;
use App\Models\Subscription;
use Carbon\Carbon;

class GetDayByUserAndDateAction
{
    public function run(int $userId, Carbon $date): Day
    {
        /** @var Subscription|Group $group */
        $subscription = Subscription::query()
            ->where('chat_id', $userId)
            ->with('group')
            ->first();

        if (!$subscription) {
            throw new UserException('Вы не подписаны ни на одну группу. Подпишитесь при помощи /subscribe.');
        }

        /** @var Day $day */
        $day = $subscription->group->days()->where('date', $date->toDateString())->latest('created_at')->first();

        if (!$day) {
            throw new UserException('В настоящее время расписание ещё не загружено (обновление каждые 15 минут), либо пар нет.');
        }

        return $day;
    }
}
