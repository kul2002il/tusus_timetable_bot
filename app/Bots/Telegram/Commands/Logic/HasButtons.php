<?php

namespace App\Bots\Telegram\Commands\Logic;

use Luzrain\TelegramBotApi\Type\InlineKeyboardButton;

trait HasButtons
{
    abstract public function runButton(string $callback): void;

    abstract public function getCommand(): string;

    protected function createButton(string $text, string $callbackData): InlineKeyboardButton
    {
        return new InlineKeyboardButton(text: $text, callbackData: "{$this->getCommand()} $callbackData");
    }
}
