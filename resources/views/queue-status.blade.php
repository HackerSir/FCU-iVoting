@extends('app')

@section('css')
    <style type="text/css">
        .popover {
            max-width: 50%;
            width: auto;
            word-break: break-all;
        }
    </style>
@endsection

@section('content')
    <div class="container container-background">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Jobs Queue</h3>
            </div>
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th class="col-md-1 text-center">#</th>
                    <th class="col-md-1">Queue</th>
                    <th class="col-md-8">Payload</th>
                    <th class="col-md-2">建立時間</th>
                </tr>
                </thead>
                <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td class="text-center">{{ $job->id }}</td>
                        <td>{{ $job->queue }}</td>
                        @if(strlen($job->payload) <= 100)
                            <td>{{ $job->payload }}</td>
                        @else
                            <td data-toggle="popover" data-placement="bottom" data-content="{{ $job->payload }}">
                                {{ mb_strimwidth($job->payload, 0, 100, "...") }}
                            </td>
                        @endif
                        <td>{{ date('Y-m-d H:i:s', $job->created_at) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td  class="text-center" colspan="5">沒有資料</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        $(function () {
            $('[data-toggle="popover"]').popover({
                container: 'body'
            })
        })
    </script>
@endsection
