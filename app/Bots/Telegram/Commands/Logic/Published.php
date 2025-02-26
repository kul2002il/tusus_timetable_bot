<?php

namespace App\Bots\Telegram\Commands\Logic;

trait Published
{
    public function getCommand(): string
    {
        return static::COMMAND;
    }

    public function getDescription(): string
    {
        return static::DESCRIPTION;
    }
}
