<!DOCTYPE html>
<html lang="zh-Hant">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta property="og:title" content="@if (trim($__env->yieldContent('title'))) @yield('title') - @endif{{ Config::get('config.sitename') }}">
        <meta property="og:url" content="{{ URL::current() }}">
        <meta property="og:image" content="{{ asset('pic/logo.jpg') }}">
        <meta property="og:description" content="iVoting 逢甲票選系統 - 一個由學生社團做的票選系統，快來參加各種票選活動吧！！！">

        <title>@if (trim($__env->yieldContent('title'))) @yield('title') - @endif{{ Config::get('config.sitename') }}</title>

        {!! HTML::style('//maxcdn.bootstrapcdn.com/bootswatch/3.3.5/united/bootstrap.min.css') !!}
        {!! HTML::style('css/fileinput.min.css') !!}
        {!! HTML::style('//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css') !!}
        {!! HTML::style('css/bootstrap-social.css') !!}
        {!! HTML::style('css/animate.css') !!}
        {!! HTML::style('css/stylesheet.css') !!}
        {!! HTML::style('css/bootstrap-datetimepicker.css') !!}
        {!! HTML::style('css/tipped/tipped.css') !!}
        {!! HTML::style('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css') !!}
        {!! HTML::style('css/select2-bootstrap.min.css') !!}
        {!! HTML::style('css/callout.css') !!}
        {!! HTML::style('css/sticky-footer-navbar.css') !!}

        @yield('css')

        <!-- Fonts -->
        {!! HTML::style('//fonts.googleapis.com/css?family=Roboto:400,300') !!}

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            {!! HTML::script('https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js') !!}
            {!! HTML::script('https://oss.maxcdn.com/respond/1.4.2/respond.min.js') !!}
        <![endif]-->

    </head>
    <body>
        {{-- navbar--}}
        @include('common.navbar')

        @yield('main-jumbotron')

        <div class="container-fluid">
            {{-- content --}}
            @yield('content')
        </div>

        <footer class="footer">
            <div class="container">
                <p class="text-muted">
                    @if(Request::is('vote-event/*') && isset($voteEvent->organizer->name))
                        主辦單位：{{ $voteEvent->organizer->name }} /
                    @endif
                    Powered by <a href="https://hackersir.info" target="_blank">逢甲大學黑客社</a>
                </p>
            </div>
        </footer>

        <!-- Scripts -->
        {!! HTML::script('//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js') !!}
        {!! HTML::script('js/fileinput.min.js') !!}
        {!! HTML::script('js/fileinput_locale_tw.js') !!}
        {!! HTML::script('//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js') !!}
        {!! HTML::script('//cdnjs.cloudflare.com/ajax/libs/holder/2.8.0/holder.min.js') !!}
        {!! HTML::script('//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js') !!}
        {!! HTML::script('js/moment_zh-tw.js') !!}
        {!! HTML::script('js/bootstrap-datetimepicker.js') !!}
        {!! HTML::script('js/bootstrap-notify.min.js') !!}
        {!! HTML::script('js/tipped/tipped.js') !!}
        {!! HTML::script('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.full.min.js') !!}
        @if(App::environment('production'))
            {!! HTML::script('js/analyticstracking.js') !!}
        @endif
        @yield('script')
        <script type="text/javascript">
            @if(Session::has('global'))
                /* Global message */
                /* Bootstrap Notify */
                $.notify({
                    // options
                    message: '{{ Session::get('global') }}'
                },{
                    // settings
                    type: 'success',
                    placement: {
                        align: 'center'
                    },
                    offset: 70,
                    delay: 5000,
                    timer: 500,
                    mouse_over: 'pause',
                    animate: {
                        enter: 'animated zoomIn',
                        exit: 'animated zoomOut'
                    }
                });
            @endif
            @if(Session::has('warning'))
                /* Warning message */
                /* Bootstrap Notify */
                $.notify({
                    // options
                    message: '{{ Session::get('warning') }}'
                },{
                    // settings
                    type: 'danger',
                    placement: {
                        align: 'center'
                    },
                    offset: 70,
                    delay: 0,
                    timer: 500,
                    mouse_over: 'pause',
                    animate: {
                        enter: 'animated rubberBand',
                        exit: 'animated zoomOut'
                    }
                });
            @endif

            $(document).ready(function() {
                Tipped.create('*',{
                    fadeIn: 0,
                    fadeOut: 0,
                    position: 'right',
                    target: 'mouse',
                    showDelay: 0,
                    hideDelay: 0,
                    offset: { x: 0, y: 15 },
                    stem: false
                });
            });

            @yield('javascript')
        </script>
    </body>
</html>
