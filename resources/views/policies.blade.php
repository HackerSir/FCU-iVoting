@extends('app')

@section('content')
    <div class="container container-background">
        <ul class="nav nav-tabs">
            <li role="presentation" @if(Request::is('policies/privacy')) class="active" @endif>
                <a href="{{ URL::route('policies', 'privacy') }}">隱私權</a>
            </li>
            <li role="presentation" @if(Request::is('policies/terms')) class="active" @endif>
                <a href="{{ URL::route('policies', 'terms') }}">服務條款</a>
            </li>
        </ul>
        <div class="panel panel-default">
            <div class="panel-body">
                @if(Request::is('policies/privacy'))
                    {!! App\MarkdownUtil::translate(File::get('privacy.md')) !!}
                @elseif(Request::is('policies/terms'))
                    {!! App\MarkdownUtil::translate(File::get('terms.md')) !!}
                @endif
            </div>
        </div>
    </div>
@endsection
