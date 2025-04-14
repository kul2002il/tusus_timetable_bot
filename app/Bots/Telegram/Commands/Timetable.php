<?php

namespace App\Bots\Telegram\Commands;

use App\Bots\Telegram\Commands\Logic\ButtonCallbackExecutable;
use App\Bots\Telegram\Commands\Logic\HasButtons;
use App\Bots\Telegram\Commands\Logic\Publicable;
use App\Bots\Telegram\Commands\Logic\Published;
use App\Models\Day;
use App\Models\Group;
use App\Models\Subscription;
use Carbon\Carbon;
use Luzrain\TelegramBotApi\Method\EditMessageText;
use Luzrain\TelegramBotApi\Method\SendMessage;
use Luzrain\TelegramBotApi\Type\InlineKeyboardMarkup;

class Timetable extends Logic\AbstractCommand implements Publicable, ButtonCallbackExecutable
{
    use Published;
    use HasButtons;

    public const COMMAND = '/timetable';

    public const DESCRIPTION = 'Посмотреть расписание на две недели.';

    private const WAIT_STATE = 1;

    public function run(int $stage = 0): void
    {
        $this->sendTimetable(Carbon::now()->toDateString());
    }

    public function runButton(string $callback): void
    {
        $this->sendTimetable($callback);
    }

    private function sendTimetable(string $date): void
    {
        /** @var Subscription|Group $group */
        $subscription = Subscription::query()
            ->where('chat_id', $this->getChatId())
            ->with('group')
            ->first();

        if (!$subscription) {
            $this->response('Вы не подписаны ни на одну группу. Подпишитесь при помощи /subscribe.');

            return;
        }

        /** @var Day $day */
        $day = $subscription->group->days()->where('date', $date)->latest('created_at')->first();

        if (!$day) {
            $this->response('В настоящее время расписание ещё не загружено (обновление каждые 15 минут), либо пар нет.');

            return;
        }

        if ($this->update->message) {
            $this->bot->call(new SendMessage(
                chatId: $this->update->message->chat->id,
                text: view('bot.day', [
                    'date'    => $date,
                    'lessons' => json_decode($day->body),
                ]),
                replyMarkup: $this->createKeyboardMarkup(),
            ));
        } else {
            $this->bot->call(new EditMessageText(
                text: view('bot.day', [
                    'date'    => $date,
                    'lessons' => json_decode($day->body),
                ]),
                chatId: $this->getChatId(),
                messageId: $this->update->callbackQuery->message->messageId,
                replyMarkup: $this->createKeyboardMarkup(),
            ));
        }
    }

    private function createKeyboardMarkup(): InlineKeyboardMarkup
    {
        $dayOfCurrentWeek = Carbon::now()->startOfWeek();
        $dayOfPreviousWeek = $dayOfCurrentWeek->clone()->subWeek();

        $dayNames = [
            'Пн',
            'Вт',
            'Ср',
            'Чт',
            'Пт',
            'Сб',
        ];

        $buttons = [];

        for ($day = 0; $day <= 5; $day++) {
            $buttons[] = [
                $this->createButton(
                    "$dayNames[$day] {$dayOfPreviousWeek->format('d')}",
                    $dayOfPreviousWeek->toDateString()
                ),
                $this->createButton(
                    "$dayNames[$day] {$dayOfCurrentWeek->format('d')}",
                    $dayOfCurrentWeek->toDateString()
                ),
            ];

            $dayOfPreviousWeek->addDay();
            $dayOfCurrentWeek->addDay();
        }

        return new InlineKeyboardMarkup($buttons);
    }
}
