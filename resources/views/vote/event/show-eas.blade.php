@extends('app')

@section('title')
    {{ $voteEvent->subject }} - 投票活動
@endsection

@section('metaTag')
    <meta name="description" property="og:description" content="{{ strip_tags(App\Helper\MarkdownHelper::translate($voteEvent->info)) }}">
@endsection

@section('css')
    {!! Minify::stylesheet([
            '/css/bootstrap-social.css',     // 社群分享按鈕
            '/css/callout.css',              // 說明框樣式
            '/css/ribbon.css',               // 排名的標籤樣式
            '/css/vote-event-show-eas.css',  // 這個頁面的自訂樣式
    ])->withFullUrl() !!}
@endsection

@section('content')
    <div class="container container-background">
        @if(Auth::check() && Auth::user()->isStaff())
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">控制面板</h3>
                </div>
                <div class="panel-body">
                    @if(!$voteEvent->isEnded())
                        <a href="{{ URL::route('vote-event.edit', ['voteEvent' => $voteEvent]) }}" class="btn btn-info"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span>編輯投票活動</a>
                    @endif
                    @if(!$voteEvent->isStarted())
                        {!! Form::open(['route' => ['vote-event.destroy', $voteEvent->id], 'style' => 'display: inline', 'method' => 'DELETE',
                        'onSubmit' => "return confirm('確定要刪除投票活動嗎？');"]) !!}
                        {!! Form::submit('刪除', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                    @endif
                    @if(Auth::user()->isAdmin())
                        @if(Request::url() != $autoRedirectSetting->data)
                            {!! Form::open(['route' => ['setting.update', $autoRedirectSetting->id], 'style' => 'display: inline' ,'class' => 'form-horizontal', 'method' => 'PUT',
                            'onSubmit' => "return confirm('確定要將此活動設為預設頁面嗎？');"]) !!}
                            {!! Form::hidden('data', Request::url()) !!}
                            {!! Form::submit('設為預設', ['class' => 'btn btn-danger', 'title' => '設為進入網站時預設導向之頁面']) !!}
                            {!! Form::close() !!}
                        @else
                            {!! Form::open(['route' => ['setting.update', $autoRedirectSetting->id], 'style' => 'display: inline' ,'class' => 'form-horizontal', 'method' => 'PUT',
                            'onSubmit' => "return confirm('確定不在將此活動作為預設頁面嗎？');"]) !!}
                            {!! Form::hidden('data', "") !!}
                            {!! Form::submit('取消預設', ['class' => 'btn btn-default', 'title' => '取消網站預設導向頁面之設定']) !!}
                            {!! Form::close() !!}
                        @endif
                    @endif
                    {{-- 控制開票事件狀態 --}}
                    @if($voteEvent->isEnded())
                        {{-- Do nothing. To maintain logic --}}
                    @elseif($voteEvent->isInProgress())
                        @if(Auth::check() && Auth::user()->isStaff())
                            {!! Form::open(['route' => ['vote-event.end', $voteEvent->id], 'style' => 'display: inline', 'method' => 'POST',
                            'onSubmit' => "return confirm('確定要立即結束此投票活動嗎？');"]) !!}
                            {!! Form::submit('立即結束', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        @endif
                    @else
                        @if(Auth::check() && Auth::user()->isStaff())
                            {!! Form::open(['route' => ['vote-event.start', $voteEvent->id], 'style' => 'display: inline', 'method' => 'POST',
                            'onSubmit' => "return confirm('確定要立即開始此投票活動嗎？');"]) !!}
                            {!! Form::submit('立即開始', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        @endif
                    @endif
                </div>
            </div>
        @endif

        <div class="jumbotron">
            @if($voteEvent->isVisible())
                @include('common.share-button-bar', ['title' => $voteEvent->subject . ' - 投票活動 - ' . Config::get('config.sitename'), 'url' => URL::current()])
            @endif
            <div class="clearfix"></div>
            {{-- h1 style comment: 加一些行高，標籤換行時才不會黏在標題下方 --}}
            <h1 style="line-height: 1.3;">
                {{ $voteEvent->subject }}
                @if($voteEvent->isEnded())
                    <span class="label label-warning label-adjust">已結束</span>
                @elseif($voteEvent->isInProgress())
                    <span class="label label-success label-adjust">進行中</span>
                @else
                    <span class="label label-default label-adjust">未開始</span>
                @endif
            </h1>

            @if($voteEvent->info)
                <blockquote>
                    {!! App\Helper\MarkdownHelper::translate($voteEvent->info) !!}
                </blockquote>
            @endif

            <ul style="font-size: 21px; padding-left: 25px;">
                <li>選出&nbsp;<strong class="text-info">{{ $voteEvent->award_count }}</strong>&nbsp;名</li>

                <li>活動期間：{!! $voteEvent->getHumanTimeString() !!}</li>

                <li>{{ $voteEvent->getResultVisibleHintText() }}</li>
            </ul>

            @if($voteEvent->organizer)
                <p>主辦單位：</p>
                <div class="row" style="margin: 0;">
                    <div class="well col-sm-6 col-md-4" style="padding: 10px;">
                        <div class="media">
                            @if($voteEvent->organizer->url)
                                <a href="{{ $voteEvent->organizer->url }}" target="_blank">
                                    @endif
                                    <div class="media-left media-middle">
                                        @if($voteEvent->organizer->logo_url)
                                            <img class="media-object" style="max-height: 75px;" src="{{ $voteEvent->organizer->logo_url }}">
                                        @endif
                                    </div>
                                    <div class="media-body" style="vertical-align: middle;">
                                        <h4 class="media-heading">{{ $voteEvent->organizer->name }}</h4>
                                    </div>
                                    @if($voteEvent->organizer->url)
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <a href="{{ URL::route('vote-event.index') }}" class="btn btn-primary pull-right" role="button"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>返回列表</a>

            <div class="clearfix"></div>
        </div>

        <div class="bs-callout bs-callout-warning" style="background: white">
            <h4>投票規則</h4>
            <ul style="font-size: 16px">
                <li>每人可以投&nbsp;<strong class="text-info" style="font-size: 25px;">{{ $voteEvent->getMaxSelected() }}</strong>&nbsp;票
                    @if(Auth::check() && Auth::user()->isStaff())
                        （設定值：{{ $voteEvent->max_selected }}）
                    @endif
                    @if(Auth::check() && $voteEvent->isStarted())
                        ，您已經投了&nbsp;<strong class="text-info" style="font-size: 25px;">{{ $voteEvent->getSelectedCount(Auth::user()) }}</strong>&nbsp;票
                    @endif
                </li>
                @if(!empty(json_decode($voteEvent->vote_condition, true)))
                    <li>投票資格限制：</li>
                    <ul>
                        @foreach($voteEvent->getConditionList(Auth::user()) as $result)
                            <li>{!! $result !!}</li>
                        @endforeach
                    </ul>
                @endif
            </ul>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">投票選項</h3>
            </div>
            <div class="panel-body">
                <div id="userPanel" class="well well-sm">
                    @if($voteEvent->isHideResult())
                        <button id="showResult" type="button" class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-search" style="margin-right: 5px;" aria-hidden="true"></span>顯示結果</button>
                    @endif
                </div>

                @if(Auth::check() && Auth::user()->isStaff() && !$voteEvent->isStarted())
                    <div class="panel" style="background-color: #f2dede;">
                        <div class="panel-body">
                            {!! HTML::linkRoute('vote-selection.create', '新增票選選項', ['vid' => $voteEvent->id], ['class' => 'btn btn-success pull-right']) !!}
                            <button class="btn btn-default" id="sortButton" type="button"><i class="fa fa-arrows"></i>調整順序</button>
                            <span class="fa" id="sortStatus"></span><span id="sortStatusMessage"></span>
                        </div>
                    </div>
                @endif

                <div class="row" id="selections">
                    @if(count($voteEvent->voteSelections))
                        @foreach($voteEvent->voteSelections as $selectionOrder => $voteSelectionItem)
                            <div class="col-sm-4 col-md-3" selection_id="{{ $voteSelectionItem->id }}">
                                <div class="thumbnail selectionBox" @if($voteSelectionItem->hasVoted(Auth::user())) style="background: #C1FFE4"@endif>
                                    @if($voteEvent->isResultVisible())
                                        <?php
                                        $scoreRank = $voteSelectionItem->scoreRank;
                                        $ribbonClass = 'ribbon-gray';
                                        if ($scoreRank == 1) {
                                            $ribbonClass = 'ribbon-gold';
                                        }
                                        elseif ($scoreRank == 2) {
                                            $ribbonClass = 'ribbon-silver';
                                        }
                                        elseif ($scoreRank == 3) {
                                            $ribbonClass = 'ribbon-bronze';
                                        }
                                        elseif ($scoreRank <= $voteEvent->award_count) {
                                            $ribbonClass = 'ribbon-blue';
                                        }
                                        ?>
                                        <div class="ribbon {{ $ribbonClass }}" data-result-hidden hidden><span>第&nbsp;{{ $scoreRank }}&nbsp;名</span></div>
                                    @endif
                                    @if($voteSelectionItem->hasVoted(Auth::user()))
                                        <div class="voted"><span class="glyphicon glyphicon-check" aria-hidden="true"></span></div>
                                    @endif
                                    <div class="vertical-center" style="height: 210px; padding-top: 10px">
                                        <div style="position: relative; z-index: 0; max-width: 100%;">
                                            @if(count($voteSelectionItem->getImageLinks()) > 0)
                                                @if(count($voteSelectionItem->getImageLinks()) > 1)
                                                    <div class="more-image-fake-shadow img-rounded"></div>
                                                @endif
                                                <img src="{{ App\Helper\ImgurHelper::thumbnail($voteSelectionItem->getImageLinks()[0], 'm') }}" class="img-rounded vote-selection" style="cursor: pointer;" data-toggle="modal" data-target="#imageModal" data-title="{{ $voteSelectionItem->title }}" data-images="{{ implode(';',$voteSelectionItem->getImageLinks()) }}"/>
                                            @else
                                                <img data-src="holder.js/200x200?text=沒有圖片&size=30" class="img-rounded vote-selection"/>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="caption">
                                        <h3 style="min-height: 31px">
                                            <span id="selectionOrder" class="numberCircle">{{ $selectionOrder+1 }}</span>
                                            {{ $voteSelectionItem->title }}
                                            @if(count($voteSelectionItem->getImageLinks()) > 0)
                                                {{-- 防止字被換行切到 --}}
                                                <small style="display: inline-block;">({{ count($voteSelectionItem->getImageLinks()) }}張照片)</small>
                                            @endif
                                        </h3>

                                        @if($voteEvent->isResultVisible())
                                            <p class="lead text-right" data-result-hidden hidden>{{ number_format($voteSelectionItem->getCount()) }}&nbsp;票</p>
                                        @endif

                                        @if($voteEvent->isInProgress())
                                            @if(!Auth::check())
                                                <div title="登入以完成投票" style="display: inline-block">
                                                    {!! HTML::linkRoute('member.login', '按此投票', [], ['class' => 'btn btn-success btn-lg']) !!}
                                                </div>
                                            @elseif(!Auth::user()->isConfirmed())
                                                <div title="投票前請先完成信箱驗證" style="display: inline-block">
                                                    {!! HTML::linkRoute('member.resend', '按此投票', [], ['class' => 'btn btn-warning btn-lg']) !!}
                                                </div>
                                            @else
                                                @if(!$voteEvent->canVote(Auth::user()))
                                                    <div title="不符合投票資格" style="display: inline-block">
                                                        <span class="btn btn-default btn-lg disabled">按此投票</span>
                                                    </div>
                                                @elseif($voteEvent->getMaxSelected() > $voteEvent->getSelectedCount(Auth::user()))
                                                    @if(!$voteSelectionItem->hasVoted(Auth::user()))
                                                        {!! Form::open(['route' => ['vote-selection.vote', $voteSelectionItem->id], 'style' => 'display: inline', 'method' => 'POST',
                                                        'onSubmit' => "return confirm('確定要投票給此項目嗎？');"]) !!}
                                                        {!! Form::submit('按此投票', ['class' => 'btn btn-success btn-lg']) !!}
                                                        {!! Form::close() !!}
                                                    @else($voteSelectionItem->hasVoted(Auth::user()))
                                                        <div title="您已經投過此項目" style="display: inline-block">
                                                            <span class="btn btn-success btn-lg disabled">按此投票</span>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div title="您已經完成投票" style="display: inline-block">
                                                        <span class="btn btn-default btn-lg disabled">按此投票</span>
                                                    </div>
                                                @endif
                                            @endif
                                        @endif

                                        @if(Auth::check() && Auth::user()->isStaff() && !$voteEvent->isStarted())
                                            {!! link_to_route('vote-selection.edit', '編輯', $voteSelectionItem->id, ['class' => 'btn btn-default']) !!}
                                            {!! Form::open(['route' => ['vote-selection.destroy', $voteSelectionItem->id], 'style' => 'display: inline', 'method' => 'DELETE',
                                            'onSubmit' => "return confirm('確定要刪除此投票選項嗎？');"]) !!}
                                            {!! Form::submit('刪除', ['class' => 'btn btn-danger']) !!}
                                            {!! Form::close() !!}
                                        @endif
                                    </div>
                                        @if(Entrust::hasRole('admin'))
                                            <div class="row" data-result-hidden hidden>
                                                <div class="col-md-3 text-center" title="權重">{{ $voteSelectionItem->weight }}</div>
                                                <div class="col-md-3 text-center" title="分數（票數*權重）">{{ $voteSelectionItem->score }}</div>
                                                <div class="col-md-3 text-center" title="原始排名">{{ $voteSelectionItem->rank }}</div>
                                                @if($voteSelectionItem->scoreRank > $voteSelectionItem->rank)
                                                    <div class="col-md-3 text-center" title="加權排名<br />與原始排名比較：<span class='glyphicon glyphicon-arrow-down'></span> -{{ $voteSelectionItem->scoreRank - $voteSelectionItem->rank }}">
                                                        {{ $voteSelectionItem->scoreRank }}<span class="glyphicon glyphicon-arrow-down"></span>
                                                    </div>
                                                @elseif($voteSelectionItem->scoreRank < $voteSelectionItem->rank)
                                                    <div class="col-md-3 text-center" title="加權排名<br />與原始排名比較：<span class='glyphicon glyphicon-arrow-up'></span> +{{ $voteSelectionItem->rank - $voteSelectionItem->scoreRank }}">
                                                        {{ $voteSelectionItem->scoreRank }}<span class="glyphicon glyphicon-arrow-up"></span>
                                                    </div>
                                                @else
                                                    <div class="col-md-3 text-center" title="加權排名<br />與原始排名相同">
                                                        {{ $voteSelectionItem->scoreRank }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div id="carousel-image" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->
                        <ol class="carousel-indicators">
                            {{-- 請保留這個，避免 bootstrap 初始化時炸掉 --}}
                            <li data-target="#carousel-image" data-slide-to="0" class="active"></li>
                            {{--<li data-target="#carousel" data-slide-to="1"></li>--}}
                        </ol>

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">
                            {{-- 請保留這個，避免 bootstrap 初始化時炸掉 --}}
                            <div class="item active">
                                <img src=""/>
                            </div>
                            {{--<div class="item">--}}
                            {{--<img src="..." />--}}
                            {{--</div>--}}
                        </div>

                        <!-- Controls -->
                        <a class="left carousel-control" href="#carousel-image" role="button" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="right carousel-control" href="#carousel-image" role="button" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    {!! HTML::script('//cdnjs.cloudflare.com/ajax/libs/holder/2.8.0/holder.min.js') !!}
    {!! HTML::script('//code.jquery.com/ui/1.11.3/jquery-ui.min.js') !!}
    {!! Minify::javascript('/js/imgur-helper.js')->withFullUrl() !!}
    <script type="text/javascript">
        {{-- 等待DOM載入完成 --}}
        $(document).ready(function () {
            refreshSelectionsShowOrder();

            @if($voteEvent->isHideResult())
                $('#showResult').click(function () {
                    $('[data-result-hidden]').each(function () {
                        $(this).toggle();
                    });
                });
            @else
                $('[data-result-hidden]').each(function () {
                    $(this).show();
                });
            @endif

            {{-- 當 userPanel 沒有元素時，將它隱藏 --}}
            $userPanel = $('#userPanel');
            if ($userPanel.children().length == 0) {
                $userPanel.hide();
            }
        });
        {{-- 等待所有資源載入完成 --}}
        $(window).load(function () {
            consistencySelectionsHeight();
        });

        var $imageModal = $('#imageModal');
        $imageModal.on('show.bs.modal', function (event) {
            var $body = $('body');

            $body.width($body.width());
            $('html').css('overflow-y', 'hidden');

            var maxHeight = $(window).height() * 0.75 + 'px';

            var clickTarget = $(event.relatedTarget);// Button that triggered the modal

            // Extract info from data-* attributes
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var recipient = clickTarget.data('title');
            var images = clickTarget.data('images').split(';');

            var $modal = $(this);
            $modal.find('.modal-title').text(recipient);

            var $div_ol = $modal.find('#carousel-image > .carousel-indicators');
            var $div_image = $modal.find('#carousel-image > .carousel-inner');

            $div_image.css('height', maxHeight);

            $div_ol.empty();
            $div_image.empty();
            $.each(images, function (index, value) {
                $div_ol.append($('<li>', {
                    "data-target": "#carousel-image",
                    "data-slide-to": index
                }));

                var $div1 = $('<div>', {
                    "class": "item"
                }).css('height', maxHeight);

                var $div2 = $('<div>').css({
                    'height': maxHeight,
                    'position': 'relative'
                });

                var src = ($body.width() >= 768) ? getImgurThumbnail(value, 'h') : getImgurThumbnail(value, 'l');
                var $img = $('<img>', {
                    "src": src,
                    "class": 'img-responsive'
                }).css({
                    'max-height': maxHeight,
                    'position': 'absolute',
                    'top': '50%',
                    'left': '50%',
                    'transform': 'translate(-50%, -50%)'
                });

                $div_image.append($div1.append($div2.append($img)));
            });
            $div_ol.children().first().addClass('active');
            $div_image.children().first().addClass('active');

            if (images.length == 1) {
                $modal.find('.left.carousel-control').hide();
                $modal.find('.right.carousel-control').hide();
            }
            else {
                $modal.find('.left.carousel-control').show();
                $modal.find('.right.carousel-control').show();
            }

        });
        $imageModal.on('hide.bs.modal', function (event) {
            $('body').removeAttr('style');
            $('html').css('overflow-y', 'scroll');
        });

        @if(Auth::check() && Auth::user()->isStaff() && !$voteEvent->isStarted())
            $('#sortButton').click(function (event) {
                var sortButton = $('#sortButton');
                var selections = $('#selections');
                var selectionBox = $('.selectionBox');
                var status = $('#sortStatus');
                var statusMessage = $('#sortStatusMessage');
                {{--  切換按鈕狀態 --}}
                sortButton.toggleClass('active');
                sortButton.children("i").toggleClass('fa-spin');
                {{--  改變排序狀態 --}}
                if (sortButton.hasClass('active')) {
                    selections.sortable({
                        tolerance: 'pointer',
                        opacity: 0.5,
                        containment: "parent",
                        placeholder: {
                            element: function (currentItem) {
                                selectionHeight = selections.children("div:first").height();
                                selectionInnerHeight = selections.children("div:first").children("div:first").height();
                                return $("<div class=\"col-sm-6 col-md-3\" style=\"height: " + selectionHeight + "px;\"><div class=\"thumbnail\" style=\"background: #eeeeee; height: " + selectionInnerHeight + "px;\"></div></div>")[0];
                            },
                            update: function (container, p) {
                                return;
                            }
                        },
                        create: function () {
                            var resize = function () {
                                $(this).css("height", "auto");
                                $(this).height($(this).height());
                            };
                            $(this).height($(this).height());
                            $(this).find('img').load(resize).error(resize);
                        }
                    });
                    selections.sortable("enable");
                    status.removeClass();
                    statusMessage.html("直接拖曳排序，完成後再次點擊按鈕即可儲存");
                    selectionBox.css("cursor", "move");
                } else {
                    selections.sortable("disable");
                    statusMessage.html("儲存中");
                    status.addClass("fa fa-refresh fa-spin");
                    selectionBox.removeAttr("style");
                    {{-- 統計順序 --}}
                    var idList = [];
                    selections.children("div").each(function () {
                        this_id = $(this).attr("selection_id");
                        idList.push(this_id);
                    });
                    {{-- 處理順序 --}}

                    var URLs = "{{ URL::route('vote-event.sort', $voteEvent->id) }}";

                    $.ajax({
                        url: URLs,
                        data: {idList: idList},
                        headers: {
                            'X-CSRF-Token': "{{ Session::token() }}",
                            "Accept": "application/json"
                        },
                        type: "POST",
                        dataType: "text",

                        success: function (data) {
                            status.removeClass();
                            if (data == "success") {
                                refreshSelectionsShowOrder();
                                consistencySelectionsHeight();

                                status.addClass("fa fa-check");
                                statusMessage.html("<span style=\"color:green\">已儲存</span>");
                            } else {
                                status.addClass("fa fa-times");
                                statusMessage.html("<span style=\"color:red\">發生錯誤：" + data + "</span>");
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            status.removeClass();
                            status.addClass("fa fa-times");
                            statusMessage.html("<span style=\"color:red\">發生錯誤：" + xhr.status + " " + thrownError + "</span>");
                        }
                    });
                }
            });
        @endif

        {{-- 更新選項的顯示編號 --}}
        function refreshSelectionsShowOrder() {
            $.each($("#selections #selectionOrder"), function (index, element) {
                $(this).text(index + 1);
            });
        }

        {{-- 讓所有選項的高度一樣 --}}
        function consistencySelectionsHeight() {
            {{-- 找出最高的高度 --}}
            var selections = $('#selections .thumbnail');
            var maxHeight = -1;
            $.each(selections, function () {
                var height = $(this).outerHeight();
                {{-- outerHeight() 包含 padding, border --}}
                if (height > maxHeight) {
                    maxHeight = height;
                }
            });
            {{-- 設定所有選項最小高度 --}}
            $.each(selections, function () {
                $(this).css('minHeight', maxHeight);
            });
        }
    </script>
@endsection
