<?php

namespace App\Bots\Telegram\Commands;

use App\Actions\GetDayByUserAndDateAction;
use App\Bots\Telegram\Commands\Logic\ButtonCallbackExecutable;
use App\Bots\Telegram\Commands\Logic\HasButtons;
use App\Bots\Telegram\Commands\Logic\Publicable;
use App\Bots\Telegram\Commands\Logic\Published;
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
        $this->sendTimetable(Carbon::now());
    }

    public function runButton(string $callback): void
    {
        $this->sendTimetable(Carbon::parse($callback));
    }

    private function sendTimetable(Carbon $date): void
    {
        $day = app(GetDayByUserAndDateAction::class)->run($this->getChatId(), $date);

        $newMessage = trim(view('bot.day', [
            'day' => $day->body,
        ]));

        if ($newMessage === $this->update->callbackQuery?->message?->text) {
            $newMessage .= "\n\nИ незачем тыкать два раза одну дату.";
        }

        if ($this->update->message) {
            $this->bot->call(new SendMessage(
                chatId: $this->update->message->chat->id,
                text: $newMessage,
                replyMarkup: $this->createKeyboardMarkup($date),
            ));
        } else {
            $this->bot->call(new EditMessageText(
                text: $newMessage,
                chatId: $this->getChatId(),
                messageId: $this->update->callbackQuery->message->messageId,
                replyMarkup: $this->createKeyboardMarkup($date),
            ));
        }
    }

    private function createKeyboardMarkup(Carbon $selectedDay): InlineKeyboardMarkup
    {
        $currentWeekIterator = Carbon::now()->startOfWeek();
        $previousWeekIterator = $currentWeekIterator->clone()->subWeek();

        $buttons = [];

        foreach (['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'] as $dayNumber => $dayName) {
            $buttons[$dayNumber] = [];

            foreach ([$previousWeekIterator, $currentWeekIterator] as $dayOfWeek) {
                $name = "$dayName {$dayOfWeek->format('d')}";

                if ($selectedDay->isSameDay($dayOfWeek)) {
                    $name = "🔶{$name}🔶";
                }

                $buttons[$dayNumber][] = $this->createButton(
                    $name,
                    $dayOfWeek->toDateString()
                );
                $dayOfWeek->addDay();
            }
        }

        return new InlineKeyboardMarkup($buttons);
    }
}
