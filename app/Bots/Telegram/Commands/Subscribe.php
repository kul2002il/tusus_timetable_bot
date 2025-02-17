<?php

namespace App\Bots\Telegram\Commands;

class Subscribe extends AbstractCommand
{
    const COMMAND = '/subscribe';

    const INPUT_GROUP_STAGE = 1;

    public function run(int $stage = 0): void
    {
        match ($stage) {
            self::INPUT_GROUP_STAGE => $this->createSubscription(),
            default => $this->init(),
        };
    }

    private function init(): void
    {
        $this->response('Введите номер группы');
        $this->setPipelineState(self::INPUT_GROUP_STAGE);
    }

    private function createSubscription(): void
    {
        $this->response('Вы подписались.');
        $this->endPipeline();
    }
}