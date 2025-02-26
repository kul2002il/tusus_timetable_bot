<?php

namespace App\Bots\Telegram\Commands\Logic;

use App\Models\Pipeline;

trait Pipelined
{
    protected function setPipelineState(int $state): void
    {
        Pipeline::query()->updateOrCreate(
            ['chat_id' => $this->update->message->chat->id],
            ['command' => static::COMMAND, 'stage' => $state],
        );
    }

    protected function endPipeline(): void
    {
        Pipeline::query()->where('chat_id', $this->update->message->chat->id)->delete();
    }
}
