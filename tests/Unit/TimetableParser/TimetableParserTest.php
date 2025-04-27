<?php

namespace Tests\Unit\TimetableParser;

use App\Scrappers\Timetable\TUSUR\TimetableParser;
use PHPUnit\Framework\TestCase;

class TimetableParserTest extends TestCase
{
    public function testSuccessParseToTimetable(): void
    {
        $source = file_get_contents(__DIR__ . '/page.html');

        $parsed = (new TimetableParser($source))->getSchedule();

        // file_put_contents(
        //     __DIR__ . '/timetable.json',
        //     json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        // );

        $this->assertSame(
            file_get_contents(__DIR__ . '/timetable.json'),
            json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
    }

    public function testSuccessGetLinkToNextWeek(): void
    {
        $source = file_get_contents(__DIR__ . '/page.html');

        $link = (new TimetableParser($source))->getNextWeekLink();

        $this->assertSame('https://timetable.tusur.ru/faculties/rkf/groups/234-2?week_id=758', $link);
    }
}
