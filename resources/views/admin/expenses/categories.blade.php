@extends('layouts.admin')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.4/css/dataTables.dataTables.css" />
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button{
        padding:0px !important;
    }
</style>


    <form action="{{ route('admin.expenses_categories') }}" method="GET" style="padding:20px;">
        @csrf
        <div class="row">
            <div class="form-group col-lg-4">
                <label for="branch_id">{{ __('Branch') }}</label>
                <select name="branch_id" id="branch_id" class="form-control">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" {{ (isset($branchId) && $branch->id == $branchId) ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-4">
                <label class="required" for="date">{{ trans('cruds.schedule.fields.date') }}</label>
                <input class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }}" type="month" name="date" id="date" value="{{ old('date', isset($date) ? $date : date('Y-m')) }}" required>
                @if($errors->has('date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.schedule.fields.date_helper') }}</span>
            </div>
            <div class="col-lg-4">
            <button type="submit" class="btn btn-primary" style="margin-top:30px;">{{ __('Filter') }}</button>

            </div>
        </div>
        
        
    </form>
    <div class="row" style="direction:rtl">
    @can('expenses_counter')
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-body" style="text-align:center">
                <h2 class="text-center">Total Amount</h2>
                <h2 class="text-center">{{ number_format($total_expenses , 2) }}</h2>
            </div>
        </div>
    </div>
    @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Expenses Categories List</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped table-hover ajaxTable datatable datatable-Expense" id="myTable">
                <thead>
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            {{ trans('cruds.expense.fields.expenses_category') }}
                        </th>
                        <th>
                            Total Amount
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
             
                <tbody>
                    @foreach($expenses_categories as $key=>$category)
                        <tr>    
                            <td>{{ ++ $key}}</td>
                            <td>{{ $category->name }}</td> 
                            <td>{{ number_format($category->total_amount, 2) }} L.E</td>                           
                            <td>
                               
                                <a href="{{route('admin.expenses_categories_show_by_filter' , [
                                        'expenses_category_id' => $category->id,
                                        'account_id' => isset($branchId) ? [$account->id] : [],
                                        'date' => isset($date) ? $date : date('Y-m') ,
                                    ])}}" class="btn btn-primary">
                                        <i class="fas fa-eye"></i>
                                </a>
                            </td>  
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>



    <script src="https://cdn.datatables.net/2.1.4/js/dataTables.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready( function () {
$('#myTable').DataTable();
} );
</script>
@endsection
@section('scripts')
    @parent
  
@endsection
