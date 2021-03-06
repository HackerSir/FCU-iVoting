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
        {!! HTML::style('//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css') !!}

        {!! Minify::stylesheet([
            '/css/stylesheet.css',            // 全域自訂 CSS
            '/css/sticky-footer.css',         // 頁尾資訊
            '/css/animate.css',               // 給 bootstrap-notify 使用，用來彈出訊息框的淡入淡出特效
            '/css/tipped.css',                // 好看的提示框
        ])->withFullUrl() !!}

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

        @if(!empty(Hackersir\Setting::get('global-notice')))
            <div class="container">
                <div class="alert alert-warning" role="alert">
                    {!! Hackersir\Setting::get('global-notice') !!}
                </div>
            </div>
        @endif

        {{-- 信箱未驗證提示--}}
        @if(Auth::check() && !Auth::user()->isConfirmed())
            <div class="container">
                <div class="alert alert-danger" role="alert">
                    <span class="glyphicon glyphicon-alert"></span> <b>請注意：</b>
                    您的帳號尚未完成{!! link_to_route('member.resend','Email驗證') !!}，可使用功能依然受限。
                    請盡快完成{!! link_to_route('member.resend','Email驗證') !!}，否則帳號將於 <span title="{{ (new Carbon(Auth::user()->created_at))->hour(5)->minute(30)->addMonth()->diffForHumans(Carbon::now())}}">{{ (new Carbon(Auth::user()->created_at))->hour(5)->minute(30)->addMonth()->formatLocalized('%Y-%m-%d') }}</span> 自動刪除。
                </div>
            </div>
        @endif

        @yield('main-jumbotron')

        {{-- content --}}
        @yield('content')

        <footer class="footer">
            <div class="container">
                <p class="text-muted">
                    Powered by <a href="https://hackersir.org" target="_blank">逢甲大學黑客社</a>
                </p>
                <ol class="breadcrumb" style="padding-top: 0; margin-bottom: 0px;">
                    <li><a href="{{ URL::route('policies', 'privacy') }}">隱私權</a></li>
                    <li><a href="{{ URL::route('policies', 'terms') }}">服務條款</a></li>
                    <li><a href="{{ URL::route('policies', 'FAQ') }}"><i class="fa fa-question-circle" aria-hidden="true" style="margin-right: 5px;"></i>常見問題</a></li>
                    <li><a href="mailto:{{ urlencode('"逢甲票選系統"') }}<ifcu.ivoting@gmail.com>" target="_blank"><span class="glyphicon glyphicon-envelope" aria-hidden="true" style="margin-right: 5px;"></span>聯絡我們</a></li>
                    @if(!empty(Hackersir\Setting::get('report-url')))
                        <li><a href="{{ Hackersir\Setting::getRaw('report-url') }}" target="_blank"><span class="glyphicon glyphicon-pencil" aria-hidden="true" style="margin-right: 5px;"></span>回報問題</a></li>
                    @endif
                    <li>
                        <a href="https://github.com/HackerSir/FCU-iVoting" target="_blank">
                            <i class="fa fa-github" aria-hidden="true"></i> GitHub
                        </a>
                    </li>
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
        {!! HTML::script('js/notify.js') !!}

        @yield('javascript')

        <script type="text/javascript">
            @if(Session::has('global'))
                /* Global message */
                notifySuccess('{!! Session::get('global') !!}');
            @endif
            @if(Session::has('warning'))
                /* Warning message */
                notifyWarning('{!! Session::get('warning') !!}', '{{ Session::get('delay', 0) }}');
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

                // Google分析
                @if(env('GOOGLE_ANALYSIS'))
                (function (i, s, o, g, r, a, m) {
                    i['GoogleAnalyticsObject'] = r;
                    i[r] = i[r] || function () {
                            (i[r].q = i[r].q || []).push(arguments)
                        }, i[r].l = 1 * new Date();
                    a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m)
                })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
                ga('create', '{{ env('GOOGLE_ANALYSIS') }}', 'auto');
                ga('send', 'pageview');
                @endif
            });
        </script>
    </body>
</html>
