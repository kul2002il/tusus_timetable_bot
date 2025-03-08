<?php

namespace App\Bots\Telegram\Commands\Logic;

interface ButtonCallbackExecutable
{
    public function runButton(string $callback): void;
}
