<?php

namespace App\Helpers\Parsers;

use Carbon\Carbon;
use PHPHtmlParser\Dom;

class TimetableParser
{
    public function __construct(private string $source)
    {
    }

    public function parseToLessons(): array
    {
        $dom = new Dom();

        $dom->loadStr($this->source);

        $elements = $dom->find('.screen-reader-element td');

        /** @var LessonDTO[] $lessons */
        $lessons = [];

        foreach ($elements as $element) {
            if (!$element->find('.lesson-cell')->count()) {
                continue;
            }
            $date = trim($element->find('.modal-body p')[1]->text());
            $times = explode('-', trim($element->find('.modal-body p')[2]->text()), 2);

            $lessons[] = new LessonDTO(
                discipline: trim($element->find('.discipline')[1]->text()),
                kind: trim($element->find('.kind')[0]->text(true)),
                auditoriums: trim($element->find('.auditoriums')[0]->text(true)),
                teachers: collect($element->find('.modal-info-teachers a'))->map(fn ($e) => $e->text(true))->all(),
                date: Carbon::parse($date),
                timeStart: Carbon::parse("$date $times[0]"),
                timeEnd: Carbon::parse("$date $times[1]"),
            );
        }

        return $lessons;
    }

    public function getTimetable(): array
    {
        $timetable = [];

        foreach ($this->parseToLessons() as $lesson) {
            $date = $lesson->date->toDateString();
            $time = $lesson->timeStart->format('H:i');
            $timetable[$date] ??= [];
            $timetable[$date][$time] = $lesson;
        }

        foreach ($timetable as $day) {
            ksort($day);
        }
        ksort($timetable);

        return $timetable;
    }
}
