@extends('layouts.admin')
@section('content')
    
        <div class="row form-group">
            <div class="col-lg-6"> 
            
            </div>
            @can('payment_counter')
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.payment.title_singular') }}</h2>
                            <h2 class="text-center">{{ $payments->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.payment.fields.amount') }}</h2>
                            <h2 class="text-center">{{ number_format($payments->sum('transaction_amount')) ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            @endcan
        </div>

    
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.payment.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Payment">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                           ID
                        </th>
                        <th>
                            User
                        </th>
                        <th>
                            Membership
                        </th>
                        <th>
                            Amount
                        </th>
                        <th>
                            #Transaction
                        </th>
                        <th>
                            #Order
                        </th>
                        <th>
                            Date
                        </th>
                        
                        <th>
                           Payment Method Type
                        </th>
                        <th>
                           Payment Method Subtype
                        </th>

                        <th>
                            Created At
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>



@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        

            let dtOverrideGlobals = {
                buttons:[],
                processing: true,
                serverSide: true,
                retrieve: true,
                searching:true,
                aaSorting: [],
                ajax: "{{ route('admin.paymobTransactions.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user',
                        name: 'user'
                    },
                    {
                        data: 'membership',
                        name: 'membership' ,
                        searchable: false
                    },
                    {
                        data: 'transaction_amount',
                        name: 'transaction_amount'
                    },
                    {
                        data: 'transaction_id',
                        name: 'transaction_id'
                    },
                    {
                        data: 'orderId',
                        name: 'orderId'
                    },
                    {
                        data: 'transaction_createdAt',
                        name: 'transaction_createdAt'
                    },
                    
                    {
                        data: 'paymentMethodType',
                        name: 'paymentMethodType'
                    },
                    {
                        data: 'paymentMethodSubType',
                        name: 'paymentMethodSubType'
                    },
                   
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                   
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 50,
            };
            let table = $('.datatable-Payment').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
