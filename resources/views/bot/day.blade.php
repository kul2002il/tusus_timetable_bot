@foreach($lessons as $time => $lesson)
{{$time}}
{{$lesson->discipline}}
{{$lesson->kind}}
{{$lesson->auditoriums}}

@endforeach