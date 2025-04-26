<?php

namespace App\Scrappers\Timetable;

use App\Models\DTO\DayScheduleDTO;
use Illuminate\Support\Collection;

interface ScheduleSourceInterface
{
    /**
     * Получить расписание на ближайший период (например, текущая и следующая недели).
     *
     * @return Collection<int, DayScheduleDTO> | DayScheduleDTO[]
     */
    public function getSchedule(): Collection;
}
