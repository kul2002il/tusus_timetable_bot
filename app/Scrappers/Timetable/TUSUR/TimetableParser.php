<?php

namespace App\Scrappers\Timetable\TUSUR;

use App\Models\DTO\DayScheduleDTO;
use App\Models\DTO\LessonDTO;
use App\Scrappers\Timetable\ScheduleSourceInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PHPHtmlParser\Dom;

class TimetableParser implements ScheduleSourceInterface
{
    private Dom $dom;

    public function __construct(string $source)
    {
        $this->dom = new Dom();
        $this->dom->loadStr($source);
    }

    private function parseToLessons(): array
    {
        $elements = $this->dom->find('.screen-reader-element td');

        /** @var LessonDTO[] $lessons */
        $lessons = [];

        foreach ($elements as $element) {
            if (!$element->find('.lesson-cell')->count()) {
                continue;
            }
            $date = trim($element->find('.modal-body p')[1]->text());
            $times = explode('-', trim($element->find('.modal-body p')[2]->text()), 2);

            $lessons[] = new LessonDTO(
                subject: trim($element->find('.discipline')[1]->text(true)),
                type: trim($element->find('.kind')[0]->text(true)),
                auditorium: trim($element->find('.auditoriums')[0]->text(true)),
                teacher: collect($element->find('.modal-info-teachers a'))->map(fn ($e) => $e->text(true))->implode(', '),
                date: Carbon::parse($date),
                startTime: Carbon::parse("$date $times[0]"),
                endTime: Carbon::parse("$date $times[1]"),
            );
        }

        return $lessons;
    }

    /** @inheritdoc */
    public function getSchedule(): Collection
    {
        $timetable = [];

        foreach ($this->parseToLessons() as $lesson) {
            $date = $lesson->date->toDateString();
            $timetable[$date] ??= new DayScheduleDTO($lesson->date->clone(), collect([]));
            $timetable[$date]->lessons->add($lesson);
        }

        foreach ($timetable as $day) {
            $day->lessons->sortBy('startTime');
        }
        ksort($timetable);

        return collect($timetable);
    }

    public function getNextWeekLink(): string
    {
        return 'https://timetable.tusur.ru' . $this->dom->find('.swiper-slide.current')->nextSibling()->nextSibling()->find('a', 0)->getAttribute('href');
    }
}
