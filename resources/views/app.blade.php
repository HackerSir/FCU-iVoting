<!DOCTYPE html>
<html lang="zh-Hant">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta property="og:title" content="@if (trim($__env->yieldContent('title'))) @yield('title') - @endif{{ Config::get('config.sitename') }}">
        <meta property="og:url" content="{{ URL::current() }}">
        <meta property="og:image" content="{{ asset('pic/logo.jpg') }}">
        @section('metaTag')
            <meta name="description" property="og:description" content="逢甲票選系統(iVoting) - 一個由學生社團做的票選系統，快來參加各種票選活動吧！！！">
        @show

        <title>@if (trim($__env->yieldContent('title'))) @yield('title') - @endif{{ Config::get('config.sitename') }}</title>

        {{-- Bootstrap United Theme--}}
        {!! HTML::style('//maxcdn.bootstrapcdn.com/bootswatch/3.3.5/united/bootstrap.min.css') !!}
        {{-- 提供超多好看的Icon --}}
        {!! HTML::style('//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css') !!}

        {!! Minify::stylesheet(array(
            '/css/stylesheet.css',            // 全域自訂 CSS
            '/css/sticky-footer-navbar.css',  // 頁尾資訊
            '/css/animate.css',               // 給 bootstrap-notify 使用，用來彈出訊息框的淡入淡出特效
            '/css/tipped.css',                // 好看的提示框
        ))->withFullUrl() !!}

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            {!! HTML::script('https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js') !!}
            {!! HTML::script('https://oss.maxcdn.com/respond/1.4.2/respond.min.js') !!}
        <![endif]-->

        @yield('css')

        @yield('head-javascript')
    </head>
    <body>
        {{-- navbar--}}
        @include('common.navbar')

        @yield('main-jumbotron')

        {{-- content --}}
        @yield('content')

        <footer class="footer">
            <div class="container">
                <p class="text-muted">
                    Powered by <a href="https://hackersir.info" target="_blank">逢甲大學黑客社</a>
                </p>
                <ol class="breadcrumb" style="padding-top: 0; margin-bottom: 0px;">
                    <li><a href="{{ URL::route('policies', 'privacy') }}">隱私權</a></li>
                    <li><a href="{{ URL::route('policies', 'terms') }}">服務條款</a></li>
                    <li><a href="{{ URL::route('policies', 'FAQ') }}">常見問題</a></li>
                    <li><a href="mailto:逢甲票選系統<ifcu.today@gmail.com>" target="_blank"><span class="glyphicon glyphicon-envelope" aria-hidden="true" style="margin-right: 5px;"></span>聯絡我們</a></li>
                    @if(env('Report_URL'))
                        <li><a href="{{ env('Report_URL') }}" target="_blank"><span class="glyphicon glyphicon-pencil" aria-hidden="true" style="margin-right: 5px;"></span>回報問題</a></li>
                    @endif
                </ol>
            </div>
        </footer>

        <!-- Scripts -->
        {!! HTML::script('//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js') !!}
        {!! HTML::script('//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js') !!}
        {{-- 好看的提示框 --}}
        {!! Minify::javascript('/js/tipped.js')->withFullUrl() !!}
        {{-- 好看的彈出訊息框 --}}
        {!! HTML::script('js/bootstrap-notify.min.js') !!}

        @if(App::environment('production'))
            {!! HTML::script('js/analyticstracking.js') !!}
        @endif

        @yield('javascript')

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
                    delay: parseInt('{{ Session::get('delay', 0) }}', 10) * 1000,
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
        </script>
    </body>
</html>
