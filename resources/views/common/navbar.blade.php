<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ URL::route('home') }}">{{ Config::get('config.sitename') }}</a>
            <ul class="nav navbar-nav">
                @include(config('laravel-menu.views.bootstrap-items'), ['items' => Menu::get('left')->roots()])
            </ul>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                @include(config('laravel-menu.views.bootstrap-items'), ['items' => Menu::get('right')->roots()])
            </ul>
        </div>
    </div>
</nav>
