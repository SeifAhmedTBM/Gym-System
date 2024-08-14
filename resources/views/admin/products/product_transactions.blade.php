@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ $product->name ?? '-' }} | Transactions </h5>
    </div>

    <div class="card-body">
        <div class="form-group">

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>
                            {{ trans('cruds.product.fields.id') }}
                        </th>

                        <th>
                            {{ trans('cruds.product.title_singular') }}
                        </th>

                        <th>
                           Type
                        </th>

                        <th>
                           From Warehouse
                        </th>
                        
                        <th>
                           To Warehouse
                        </th>

                        <th>
                            Quantity
                        </th>

                        <th>
                            {{ trans('global.note_text') }}
                        </th>

                        <th>
                            {{ trans('cruds.expense.fields.created_by') }}
                        </th>

                        <th>
                            {{ trans('global.created_at') }}
                        </th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($product->transactions as $transaction)
                        <tr>
                            <td>
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                {{ $transaction->product->name }}
                            </td>
                            
                            <td>
                                <span class="badge {{ \App\Models\ProductTransactions::color[$transaction->type] }}">
                                    {{ \App\Models\ProductTransactions::type[$transaction->type] }}
                                </span>
                            </td>
                            <td>
                                {{ $transaction->fromWarehouse->name ?? '-' }}
                            </td>
                            <td>
                                {{ $transaction->toWarehouse->name ?? '-' }}
                            </td>
                            <td>
                                {{ $transaction->quantity }}
                            </td>
                            <td>
                                {{ $transaction->notes }}
                            </td>
                            <td>
                                {{ $transaction->createdBy->name ?? '-' }}
                            </td>
                            <td>
                                {{ $transaction->created_at }}
                            </td>
                            
                        </tr>
                    @empty
                        <td colspan="9" class="text-center">No data available</td>
                    @endforelse
                </tbody>
            </table>

            <div class="form-group">
                {{-- <a class="btn btn-default" href="{{ route('admin.warehouses.show',$warehouse->id) }}">
                    {{ trans('global.back_to_list') }}
                </a> --}}
            </div>
        </div>
    </div>
</div>


@endsection
