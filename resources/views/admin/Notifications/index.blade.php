@extends('layouts.admin')
@section('content')
    @can('timeslot_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.notification.create') }}">
                    Send Notifications
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
       
        <div class="card-header">
            <h5>All Notifications</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Timeslot">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.timeslot.fields.id') }}
                            </th>
                            <th>
                               Title
                            </th>
                            <th>
                                Body
                            </th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $key => $notification)
                            <tr data-entry-id="{{ $notification->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $notification->id ?? '' }}
                                </td>
                                <td>
                                    {{ $notification->title }}
                                </td>
                                <td>
                                    {{ $notification->body }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            });
            let table = $('.datatable-Timeslot:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
