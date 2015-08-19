<li @if((Request::is(str_replace('!','',$uri)) && $uri!="/") || Request::route()->getPath()==str_replace('!','',$uri)) class="active" @endif>
    <a href="{{ URL::to(str_replace('!','',$uri)) }}" @if(strpos($uri,'://') !== false || starts_with($uri, '!')) target="_blank" @endif>
        {!! (Auth::check())?str_replace('%user%',Auth::user()->getNickname(),$name):$name !!}
        @if(strpos($uri,'://') !== false || starts_with($uri, '!'))
            <i class="glyphicon glyphicon-new-window"></i>
        @endif
    </a>
</li>
