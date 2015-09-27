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
                <h3 class="panel-title">Queue Status</h3>
            </div>
            <table class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th class="col-md-3">Name</th>
                    <th class="col-md-6">Description</th>
                    <th class="col-md-3">State</th>
                </tr>
                </thead>
                <tbody>
                @if(is_array($queues))
                    @forelse($queues as $queue)
                        <tr>
                            <td>{{ $queue['name'] }}</td>
                            <td>{{ $queue['description'] }}</td>
                            <td>{{ $queue['state'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="3">沒有資料</td>
                        </tr>
                    @endforelse
                @else
                    <tr>
                        <td class="text-center" colspan="3">{{ $queues }}</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Jobs Queue</h3>
            </div>
            <table class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th class="col-md-1 text-center">#</th>
                    <th class="col-md-1">Queue</th>
                    <th class="col-md-8">Payload</th>
                    <th class="col-md-2 text-center">建立時間</th>
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
                        <td class="text-center">{{ date('Y-m-d H:i:s', $job->created_at) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td  class="text-center" colspan="4">沒有資料</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Failed Jobs</h3>
            </div>
            <table class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th class="col-md-1 text-center">#</th>
                    <th class="col-md-2">Connection</th>
                    <th class="col-md-1">Queue</th>
                    <th class="col-md-6">Payload</th>
                    <th class="col-md-2 text-center">失敗時間</th>
                </tr>
                </thead>
                <tbody>
                @forelse($failedJobs as $failedJob)
                    <tr>
                        <td class="text-center">{{ $failedJob->id }}</td>
                        <td>{{ $failedJob->connection }}</td>
                        <td>{{ $failedJob->queue }}</td>
                        @if(strlen($failedJob->payload) <= 100)
                            <td>{{ $failedJob->payload }}</td>
                        @else
                            <td data-toggle="popover" data-placement="bottom" data-content="{{ $failedJob->payload }}">
                                {{ mb_strimwidth($failedJob->payload, 0, 100, "...") }}
                            </td>
                        @endif
                        <td class="text-center">{{ $failedJob->failed_at }}</td>
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
