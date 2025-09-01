<?php

namespace App\Console\Commands;

use App\Models\Group;
use Illuminate\Console\Command;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;

class PullGroups extends Command
{
    protected $signature = 'timetable:pull-groups';

    protected $description = 'Get all groups from timetable.tusur.ru';

    public function handle()
    {
        $dom = new Dom();
        $dom->loadFromUrl('https://timetable.tusur.ru/faculties');

        /** @var HtmlNode[] $facultiesLinks */
        $facultiesLinks = $dom->find('a[href^="/faculties/"]');

        $this->withProgressBar($facultiesLinks, function ($facultyLink) {
            $faculty = str_replace('/faculties/', '', $facultyLink->getAttribute('href'));

            $dom = new Dom();
            $dom->loadFromUrl("https://timetable.tusur.ru/faculties/$faculty");

            $groupsLinks = $dom->find("a[href^=\"/faculties/$faculty/groups/\"]");

            foreach ($groupsLinks as $groupLink) {
                $group = str_replace("/faculties/$faculty/groups/", '', $groupLink->getAttribute('href'));
                $record = Group::query()->firstOrNew([
                    'number' => $group,
                ]);
                $record->faculty = $faculty;
                $record->save();
            }
        });
    }
}
