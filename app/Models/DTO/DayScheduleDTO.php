<?php

namespace App\Models\DTO;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use JsonSerializable;

class DayScheduleDTO implements JsonSerializable
{
    /**
     * @param Carbon $date
     * @param Collection<int, LessonDTO> $lessons
     */
    public function __construct(
        public readonly Carbon $date,
        public readonly Collection $lessons,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            date: Carbon::parse($data['date']),
            lessons: collect($data['lessons'])->map(
                fn (array $lessonData) => LessonDTO::fromArray($lessonData)
            )
        );
    }

    public function toArray(): array
    {
        return [
            'date'    => $this->date->toDateString(),
            'lessons' => $this->lessons->map(
                fn (LessonDTO $lesson) => $lesson->toArray()
            )->all(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
