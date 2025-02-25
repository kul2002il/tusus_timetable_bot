<?php

namespace Tests\Unit\TimetableParser;

use App\Helpers\Parsers\TimetableParser;
use PHPUnit\Framework\TestCase;

class TimetableParserTest extends TestCase
{
    public function testSuccessParseToLessonsArray(): void
    {
        $source = file_get_contents(__DIR__ . '/page.html');

        $parsed = (new TimetableParser($source))->parseToLessons();

        // file_put_contents(
        //     __DIR__ . '/parsedLessons.json',
        //     json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        // );

        $this->assertSame(
            file_get_contents(__DIR__ . '/parsedLessons.json'),
            json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
    }

    public function testSuccessParseToTimetable(): void
    {
        $source = file_get_contents(__DIR__ . '/page.html');

        $parsed = (new TimetableParser($source))->getTimetable();

        // file_put_contents(
        //     __DIR__ . '/timetable.json',
        //     json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        // );

        $this->assertSame(
            file_get_contents(__DIR__ . '/timetable.json'),
            json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
    }
}
