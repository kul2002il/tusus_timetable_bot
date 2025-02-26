<?php

namespace App\Bots\Telegram\Commands\Logic;

interface Publicable
{
    public function getCommand(): string;

    public function getDescription(): string;
}
