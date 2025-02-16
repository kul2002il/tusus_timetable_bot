<?php

namespace Tests\Unit\TimetableParser;

use App\Helpers\Parsers\TimeTableParser;
use PHPUnit\Framework\TestCase;

class TimetableParserTest extends TestCase
{
    public function test_example(): void
    {
        $sourse = file_get_contents(__DIR__ . '/page.html');

        $parsed = (new TimeTableParser())->parseStringToLessons($sourse);

        // file_put_contents(
        //     __DIR__ . '/parsedLessons.json',
        //     json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        // );

        $this->assertSame(
            $parsed,
            json_decode(file_get_contents(__DIR__ . '/parsedLessons.json'), true)
        );
    }
}
