<?php

namespace App\Models\DTO;

use Carbon\Carbon;
use JsonSerializable;

class LessonDTO implements JsonSerializable
{
    public function __construct(
        public readonly string $subject,
        public readonly string $type,
        public readonly string $auditorium,
        public readonly string $teacher,
        public readonly Carbon $date,
        public readonly Carbon $startTime,
        public readonly Carbon $endTime,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            subject: $data['subject'],
            type: $data['type'],
            auditorium: $data['auditorium'],
            teacher: $data['teacher'],
            date: Carbon::parse($data['date']),
            startTime: Carbon::parse($data['start_time']),
            endTime: Carbon::parse($data['end_time']),
        );
    }

    public function toArray(): array
    {
        return [
            'subject'    => $this->subject,
            'type'       => $this->type,
            'auditorium' => $this->auditorium,
            'teacher'    => $this->teacher,
            'date'       => $this->date->toDateString(),
            'start_time' => $this->startTime->format('H:i'),
            'end_time'   => $this->endTime->format('H:i'),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
