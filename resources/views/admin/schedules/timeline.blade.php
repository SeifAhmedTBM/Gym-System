@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="fa fa-table"></i> {{ trans('global.schedule_timeline') }}</h5>
        <button type="button" onclick="PrintElem(this)" class="btn btn-primary btn-sm float-right">
            <i class="fa fa-print"></i> {{ trans('global.print') }}
        </button>
    </div>
    <div class="card-body table-responsive" id="printMe">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th class="bg-primary text-dark" width="150">{{ trans('global.timeslots') }}</th>
                    @foreach (App\Models\Schedule::DAY_SELECT as $key => $day)
                        <th class="font-weight-bold text-dark">{{ $day }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($timeslots as $timeslot)
                <tr>
                    <td class="font-weight-bold">{{ date('g:i A', strtotime($timeslot->from)) }}</td>
                    @foreach (App\Models\Schedule::DAY_SELECT as $k => $d)
                        @if ($sch_day = $timeslot->schedules()->where('day', $k)->get())
                        <td class="font-weight-bold">
                            @forelse ($sch_day as $item)
                                <span class="badge  mb-1 py-2 px-2 text-white" style="background: {{ $item->session->color }}">
                                    {{ $item->session->name }} ( {{ $item->trainer->name }} )
                                </span>
                                @empty
                                ----
                            @endforelse
                        </td>
                        @endif
                    @endforeach
                </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">{{ trans('global.no_data_available') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function PrintElem()
    {
        var divToPrint=document.getElementById('printMe');

        var newWin=window.open('','Print-Window');

        newWin.document.open();

        newWin.document.write('<html><link href="{{ asset("css/coreui.min.css")}}" rel="stylesheet" type="text/css" /><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

        newWin.document.close();

        setTimeout(function(){newWin.close();},100);
    }
</script>
@endsection