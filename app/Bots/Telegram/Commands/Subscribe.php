<?php

namespace App\Bots\Telegram\Commands;

use App\Bots\Telegram\Commands\Logic\AbstractCommand;
use App\Bots\Telegram\Commands\Logic\Pipelined;
use App\Bots\Telegram\Commands\Logic\Publicable;
use App\Bots\Telegram\Commands\Logic\Published;
use App\Models\Group;
use App\Models\Subscription;

class Subscribe extends AbstractCommand implements Publicable
{
    use Published;
    use Pipelined;

    public const COMMAND = '/subscribe';
    public const DESCRIPTION = 'Подписаться на расписание группы.';

    public const INPUT_GROUP_STAGE = 1;

    public function run(int $stage = 0): void
    {
        match ($stage) {
            self::INPUT_GROUP_STAGE => $this->createSubscription(),
            default                 => $this->init(),
        };
    }

    private function init(): void
    {
        $this->response('Введите номер группы');
        $this->setPipelineState(self::INPUT_GROUP_STAGE);
    }

    private function createSubscription(): void
    {
        /** @var Group $group */
        $group = Group::query()->where('number', $this->update->message->text)->first();

        if (!$group) {
            $this->response("Такой группы нет.");
            $this->endPipeline();

            return;
        }

        Subscription::query()->updateOrCreate(
            ['chat_id' => $this->update->message->chat->id],
            ['group_id' => $group->id, 'options' => '{}'],
        );

        $this->response("Вы подписались на группу $group->number, $group->faculty. Можно попробовать запросить расписание командой /today.");
        $this->endPipeline();
    }
}
