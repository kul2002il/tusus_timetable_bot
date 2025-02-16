<?php

namespace App\Helpers\Parsers;

use PHPHtmlParser\Dom;

class TimetableParser
{
    public function parseStringToLessons(string $page): array
    {
        $dom = new Dom();

        $dom->loadStr($page);

        $elements = $dom->find('.screen-reader-element td');

        $lessons = [];

        foreach ($elements as $element) {
            if (!$element->find('.lesson-cell')->count()) {
                continue;
            }
            $lessons[] = [
                'discipline'    => trim($element->find('.discipline')[1]->text()),
                'kind'          => trim($element->find('.kind')[0]->text(true)),
                'auditoriums'   => trim($element->find('.auditoriums')[0]->text(true)),
                'teachers'      => collect($element->find('.modal-info-teachers a'))->map(fn ($e) => $e->text(true))->all(),
                'date'          => trim($element->find('.modal-body p')[1]->text()),
                'time'          => trim($element->find('.modal-body p')[2]->text()),
            ];
        }

        return $lessons;
    }
}
