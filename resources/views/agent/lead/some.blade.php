@extends('layouts.master')

{{-- Content --}}
@section('content')
    <div class="_page-header" xmlns="http://www.w3.org/1999/html">
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-md-8">
                <table class="table table-bordered table-striped table-hover dataTable">
                    <thead>
                    <tr>
                        <th>{!! trans("main.icon") !!}</th>
                        <th>{!! trans("site/lead.date") !!}</th>
                        <th>{!! trans("site/lead.name") !!}</th>
                        <th>{!! trans("site/lead.phone") !!}</th>
                        <th>{!! trans("site/lead.email") !!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($leads as $lead)
                        <tr class="ajaxTr" data-item-id="{!! $lead->id !!}">
                            <td></td>
                            <td>{!! $lead->date !!}</td>
                            <td>{!! $lead->name !!}</td>
                            <td>{!! $lead->phone->phone !!}</td>
                            <td>{!! $lead->email !!}</td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <div class="col-md-4">
                    <div id="lead_info">

                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
            $(".ajaxTr").click(function () {
                var itemId = $(this).data('item-id');
                console.log(itemId);
                $.ajax({
                    url: "{{ route('agent.lead.some.full') }}",
                    method: 'GET',
                    data: {
                        itemId: itemId
                    },
                    success: function (data) {
                        $("#lead_info").html(data);
                    }
                });
            })
        });
    </script>
@stop