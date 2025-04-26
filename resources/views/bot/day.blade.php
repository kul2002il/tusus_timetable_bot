@php
/** @var \App\Models\DTO\DayScheduleDTO $day */
@endphp

Расписание на {{ $day->date->toDateString() }}:

@foreach($day->lessons as $lesson)
{{$lesson->startTime->format('H:i')}}
{{$lesson->subject}}
{{$lesson->type}}
{{$lesson->auditorium}}

@endforeach