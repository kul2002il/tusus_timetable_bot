<?php

namespace App\Console\Commands;

use App\Helpers\Parsers\TimetableParser;
use App\Models\Day;
use App\Models\Group;
use Illuminate\Console\Command;

class PullTimetable extends Command
{
    protected $signature = 'timetable:pull';

    protected $description = 'Get all timetables from timetable.tusur.ru.';

    public function handle()
    {
        foreach (Group::all() as $group) {
            $source = file_get_contents("https://timetable.tusur.ru/faculties/{$group->faculty}/groups/{$group->number}");

            $timetable = (new TimetableParser($source))->getTimetable();

            foreach ($timetable as $date => $dayLessons) {
                $day = Day::query()->firstOrNew([
                    'group_id' => $group->id,
                    'date' => $date
                ]);

                $currentVersion = json_encode($dayLessons, JSON_UNESCAPED_UNICODE);

                if ($day->body !== $currentVersion) {
                    dump("$date was changed!");
                    dump($day->body);
                    dump($currentVersion);
                    $day->body = $currentVersion;
                }

                $day->touch();
                $day->save();
            }
        }
    }
}
