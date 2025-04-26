<?php

namespace App\Models\DTO;

use Carbon\Carbon;

class LessonDTO
{
    /**
     * @param string $discipline
     * @param string $kind
     * @param string $auditoriums
     * @param string[] $teachers
     * @param Carbon $date
     * @param Carbon $timeStart
     * @param Carbon $timeEnd
     */
    public function __construct(
        public string $discipline,
        public string $kind,
        public string $auditoriums,
        public array  $teachers,
        public Carbon $date,
        public Carbon $timeStart,
        public Carbon $timeEnd,
    ) {
    }
}
