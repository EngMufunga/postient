@extends('Template::layouts.frontend')

@section('content')


    @if($sections != null)
        @foreach(json_decode($sections) as $sec)
            @includeIf('Template::sections.'.$sec)
        @endforeach
    @endif
@endsection
