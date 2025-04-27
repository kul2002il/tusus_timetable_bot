<?php

namespace App\Console\Commands;

use App\Models\Day;
use App\Models\Group;
use App\Models\Subscription;
use App\Scrappers\Timetable\TUSUR\TimetableParser;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class PullTimetable extends Command
{
    protected $signature = 'timetable:pull';

    protected $description = 'Get all timetables from timetable.tusur.ru.';

    public function handle()
    {
        $ids = Subscription::query()->select('group_id')->distinct()->get();

        $this->withProgressBar(Group::query()->whereIn('id', $ids)->get(), function (Group $group) {
            $source = file_get_contents("https://timetable.tusur.ru/faculties/{$group->faculty}/groups/{$group->number}");

            $timetableParser = new TimetableParser($source);

            $this->saveTimetable($timetableParser->getSchedule(), $group);

            sleep(5);

            $this->saveTimetable((
                new TimetableParser(file_get_contents($timetableParser->getNextWeekLink()))
            )->getSchedule(), $group);

            sleep(5);
        });
    }

    private function saveTimetable(Collection $timetable, Group $group): void
    {
        foreach ($timetable as $scheduleDay) {
            $date = $scheduleDay->date->toDateString();
            /** @var Day $day */
            $day = Day::query()->firstOrNew([
                'group_id' => $group->id,
                'date'     => $date
            ]);

            if ($day->body?->toArray() != $scheduleDay->toArray()) {
                dump("$date was changed!");
                $day->body = $scheduleDay;
            }

            $day->touch();
            $day->save();
        }
    }
}
