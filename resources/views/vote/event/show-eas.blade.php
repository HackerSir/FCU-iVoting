@extends('app')

@section('title')
    {{ $voteEvent->subject }} - 投票活動
@endsection

@section('content')
    <div class="container">
        @if(Auth::check() && Auth::user()->isStaff())
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">控制面板</h3>
                </div>
                <div class="panel-body">
                    @if(!$voteEvent->isEnded())
                        {!! HTML::linkRoute('vote-event.edit', '編輯投票活動', $voteEvent->id, ['class' => 'btn btn-info']) !!}
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
            <h1>
                {{ $voteEvent->subject }}
                <span class="label label-success" style="font-size: 70%; margin-left: 10px; position: relative; top: -5px;">
                    @if($voteEvent->isEnded())
                        已結束
                    @elseif($voteEvent->isInProgress())
                        進行中
                    @else
                        未開始
                    @endif
                </span>
            </h1>

            <p>{!! Markdown::parse(htmlspecialchars($voteEvent->info)) !!}</p>

            <p>活動期間：{{ $voteEvent->getHumanTimeString() }}</p>

            {!! HTML::linkRoute('vote-event.index', '返回投票活動列表', [], ['class' => 'btn btn-default pull-right']) !!}
        </div>

        <div class="bs-callout bs-callout-warning">
            <h4>投票規則</h4>
            <ul>
                <li>每人最多可以投&nbsp;{{ $voteEvent->getMaxSelected() }}&nbsp;票
                    @if(Auth::check() && Auth::user()->isStaff())
                        （設定值：{{ $voteEvent->max_selected }}）
                    @endif
                </li>
                <li>採相對多數決(也就是最高票獲選)</li>
            </ul>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">投票選項</h3>
            </div>
            <div class="panel-body">
                @if(Auth::check() && Auth::user()->isStaff() && !$voteEvent->isStarted())
                    <div class="panel" style="background-color: #f2dede;">
                        <div class="panel-body">
                            {!! HTML::linkRoute('vote-selection.create', '新增投票選項', ['vid' => $voteEvent->id], ['class' => 'btn btn-success pull-right']) !!}
                        </div>
                    </div>
                @endif

                <div class="row">
                    @if(count($voteEvent->voteSelections))
                        @foreach($voteEvent->voteSelections as $voteSelectionItem)
                            <div class="col-sm-6 col-md-4">
                                <div class="thumbnail">
                                    <div class="text-center" style="height: 300px;">
                                    @if(count($voteSelectionItem->getImageLinks()) > 0)
                                        <img src="{{ App\Imgur::thumbnail($voteSelectionItem->getImageLinks()[0], 'm') }}" class="img-rounded" style="max-width:100%;max-height:300px;width:auto;height:auto;" title="點此看更多圖" data-toggle="modal" data-target="#imageModal" data-title="{{ $voteSelectionItem->getTitle() }}" data-images="{{ implode(';',$voteSelectionItem->getImageLinks()) }}" />
                                    @else
                                        <img data-src="holder.js/300x300?text=沒有圖片&size=45" class="img-rounded" style="max-width:100%;max-height:300px;width:auto;height:auto;" />
                                    @endif
                                    </div>
                                    <div class="caption">
                                        <h3>
                                            @if($voteEvent->isEnded() && $voteSelectionItem->isMax())
                                                <span title="最高票" class="glyphicon glyphicon-king" aria-hidden="true" style="color: blue;"></span>
                                                <span class="sr-only">最高票</span>
                                            @endif
                                            {!! HTML::linkRoute('vote-selection.show', $voteSelectionItem->getTitle(),$voteSelectionItem->id, null) !!}
                                            @if(Auth::check() && $voteSelectionItem->hasVoted(Auth::user()))
                                                <span title="我的選擇" class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                                <span class="sr-only">我的選擇</span>
                                            @endif
                                        </h3>

                                        @if($voteEvent->isEnded())
                                            <p class="lead text-right">{{ $voteSelectionItem->getCount() }}&nbsp;票</p>
                                        @endif

                                        @if(Auth::check() && Auth::user()->isStaff() && !$voteEvent->isStarted())
                                            {!! link_to_route('vote-selection.edit', '編輯', $voteSelectionItem->id, ['class' => 'btn btn-default']) !!}
                                            {!! Form::open(['route' => ['vote-selection.destroy', $voteSelectionItem->id], 'style' => 'display: inline', 'method' => 'DELETE',
                                            'onSubmit' => "return confirm('確定要刪除此投票選項嗎？');"]) !!}
                                            {!! Form::submit('刪除', ['class' => 'btn btn-danger']) !!}
                                            {!! Form::close() !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-lg" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
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
                            <li data-target="#carousel" data-slide-to="0" class="active"></li>
                            {{--<li data-target="#carousel" data-slide-to="1"></li>--}}
                        </ol>

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">
                            {{-- 請保留這個，避免 bootstrap 初始化時炸掉 --}}
                            <div class="item active">
                                <img src="" />
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

@section('script')

    <script type="text/javascript">
        $('#imageModal').on('show.bs.modal', function (event) {
            $('body').width($('body').width());
            $('html').css('overflow-y', 'hidden');

            var clickTarget = $(event.relatedTarget);// Button that triggered the modal

            // Extract info from data-* attributes
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var recipient = clickTarget.data('title');
            var images = clickTarget.data('images').split(';');

            var modal = $(this);
            modal.find('.modal-title').text(recipient);

            var div_ol = modal.find('#carousel-image > .carousel-indicators');
            var div_image = modal.find('#carousel-image > .carousel-inner');
            div_ol.empty();
            div_image.empty();
            $.each(images, function(index, value) {
                div_ol.append('<li data-target="#carousel-image" data-slide-to="' + index +  '"></li>');
                div_image.append('<div class="item"><img src="'+ value +'" /></div>');
            });
            div_ol.children().first().addClass('active');
            div_image.children().first().addClass('active');

            if (images.length == 1) {
                modal.find('.left.carousel-control').hide();
                modal.find('.right.carousel-control').hide();
            }
            else {
                modal.find('.left.carousel-control').show();
                modal.find('.right.carousel-control').show();
            }

        });
        $('#imageModal').on('hide.bs.modal', function (event) {
            $('html').css('overflow-y', 'scroll');
        });
    </script>

@endsection
