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
            <li role="presentation" @if(Request::is('policies/FAQ')) class="active" @endif>
                <a href="{{ URL::route('policies', 'FAQ') }}">常見問題</a>
            </li>
        </ul>
        <div class="panel panel-default">
            <div class="panel-body" style="font-size: larger;">
                @if(Request::is('policies/privacy'))
                    {!! Markdown::parse(File::get('privacy.md')) !!}
                @elseif(Request::is('policies/terms'))
                    {!! Markdown::parse(File::get('terms.md')) !!}
                @elseif(Request::is('policies/FAQ'))
                    {!! Markdown::parse(File::get('faq.md')) !!}
                @endif
            </div>
        </div>
    </div>
@endsection
