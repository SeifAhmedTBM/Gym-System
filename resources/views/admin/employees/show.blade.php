@extends('layouts.admin')
@section('content')
    <div class="form-group row">
        <div class="col-md-2">
            <div class="dropdown">
                <a class="btn btn-primary btn-block dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                    aria-expanded="false">
                    {{ trans('global.action') }}
                </a>
            
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a href="{{ route('admin.employees.edit',$employee->id) }}" class="dropdown-item">
                        <i class="fa fa-edit"></i> &nbsp; {{ trans('global.edit') }}
                    </a>

                    <a href="{{ route('admin.employees.add_bonus',$employee->id) }}" class="dropdown-item">
                        <i class="fa fa-plus"></i> &nbsp; {{ trans('global.add') }} Bonus
                    </a>

                    <a href="{{ route('admin.employees.add_deduction',$employee->id) }}" class="dropdown-item">
                        <i class="fa fa-plus"></i> &nbsp; {{ trans('global.add') }} Deduction
                    </a>

                    <a href="{{ route('admin.employees.add_loan',$employee->id) }}" class="dropdown-item">
                        <i class="fa fa-plus"></i> &nbsp; {{ trans('global.add') }} Loan
                    </a>

                    <a href="{{ route('admin.employees.add_vacation',$employee->id) }}" class="dropdown-item">
                        <i class="fa fa-plus"></i> &nbsp; {{ trans('global.add') }} Vacation
                    </a>

                    <a href="{{ route('admin.employees.add_document',$employee->id) }}" class="dropdown-item">
                        <i class="fa fa-plus"></i> &nbsp; {{ trans('global.add') }} Document
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="nav  nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="basic_data-tab" data-toggle="pill" onclick="changeUrlTab(this)"
                            href="#basic_data" role="tab" aria-controls="basic_data" aria-selected="true">
                            {{ trans('global.basic_data') }}
                        </a>

                        <a class="nav-link" id="bonuses-tab" data-toggle="pill" onclick="changeUrlTab(this)"
                            href="#bonuses" role="tab" aria-controls="bonuses" aria-selected="true">
                            {{ trans('cruds.bonu.title') }}
                        </a>

                        <a class="nav-link" id="deductions-tab" data-toggle="pill" onclick="changeUrlTab(this)"
                            href="#deductions" role="tab" aria-controls="deductions" aria-selected="true">
                            {{ trans('cruds.deduction.title') }}
                        </a>

                        <a class="nav-link" id="loans-tab" data-toggle="pill" onclick="changeUrlTab(this)"
                            href="#loans" role="tab" aria-controls="loans" aria-selected="true">
                            {{ trans('cruds.loan.title') }}
                        </a>

                        <a class="nav-link" id="vacations-tab" data-toggle="pill" onclick="changeUrlTab(this)"
                            href="#vacations" role="tab" aria-controls="vacations" aria-selected="true">
                            {{ trans('cruds.vacation.title') }}
                        </a>

                        <a class="nav-link" id="documents-tab" data-toggle="pill" onclick="changeUrlTab(this)"
                            href="#documents" role="tab" aria-controls="documents" aria-selected="true">
                            {{ trans('cruds.document.title') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-user"></i> {{ $employee->name }}</h4>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="basic_data" role="tabpanel" aria-labelledby="basic_data-tab">
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <a href="{{ $employee->photo->url ?? '' }}" style="display: inline-block">
                                                <img src="{{ $employee->photo->thumbnail ?? '' }}" class="rounded-circle"
                                                    style="width: 150px;height:150px">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5> Code </h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $employee->id }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.lead.fields.name') }} </h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $employee->name }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.lead.fields.phone') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $employee->phone }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('global.finger_print_id') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $employee->finger_print_id ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>Access Card</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $employee->access_card ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.employee.fields.job_status') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ App\Models\Employee::JOB_STATUS_SELECT[$employee->job_status] ?? '' }}
                                        </div>
                                    </div>
                                    
                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.employee.fields.start_date') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $employee->start_date }}
                                        </div>
                                    </div>
                                    
                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('global.attendance_check') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ App\Models\Employee::CARD_CHECK_SELECT[$employee->attendance_check] ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.employee.fields.salary') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $employee->salary }} <b>EGP</b>
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.employee.fields.status') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ App\Models\Employee::STATUS_SELECT[$employee->status] ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('global.vacations_balance') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ number_format($employee->vacations_balance - $employee->vacations_sum_diff) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="bonuses" role="tabpanel" aria-labelledby="bonuses-tab">
                            <table class="table table-striped table-hover table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Reason</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee->bonuses as $bonus)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $bonus->name }}</td>
                                            <td>{{ $bonus->amount }}</td>
                                            <td>{{ $bonus->reason }}</td>
                                            <td>{{ $bonus->created_by->name }}</td>
                                            <td>{{ $bonus->created_at }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                                                        aria-expanded="false">
                                                        {{ trans('global.action') }}
                                                    </a>
                                                
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <a href="{{ route('admin.bonus.show',$bonus->id) }}" class="dropdown-item">
                                                            <i class="fa fa-eye"></i> &nbsp; View
                                                        </a>

                                                        <a href="{{ route('admin.bonus.edit',$bonus->id) }}" class="dropdown-item">
                                                            <i class="fa fa-edit"></i> &nbsp; Edit
                                                        </a>

                                                        <form action="{{ route('admin.bonus.destroy', $bonus->id) }}" method="POST"
                                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade" id="deductions" role="tabpanel" aria-labelledby="deductions-tab">
                            <table class="table table-striped table-hover table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Reason</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee->deductions as $deduction)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $deduction->name }}</td>
                                            <td>{{ $deduction->amount }}</td>
                                            <td>{{ $deduction->reason }}</td>
                                            <td>{{ $deduction->created_by->name }}</td>
                                            <td>{{ $deduction->created_at }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                                                        aria-expanded="false">
                                                        {{ trans('global.action') }}
                                                    </a>
                                                
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <a href="{{ route('admin.deductions.show',$deduction->id) }}" class="dropdown-item">
                                                            <i class="fa fa-eye"></i> &nbsp; View
                                                        </a>

                                                        <a href="{{ route('admin.deductions.edit',$deduction->id) }}" class="dropdown-item">
                                                            <i class="fa fa-edit"></i> &nbsp; Edit
                                                        </a>

                                                        <form action="{{ route('admin.deductions.destroy', $deduction->id) }}" method="POST"
                                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade" id="loans" role="tabpanel" aria-labelledby="loans-tab">
                            <table class="table table-striped table-hover table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee->loans as $loan)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $loan->name }}</td>
                                            <td>{{ $loan->amount }}</td>
                                            <td>{{ $loan->description }}</td>
                                            <td>{{ $loan->created_by->name }}</td>
                                            <td>{{ $loan->created_at }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                                                        aria-expanded="false">
                                                        {{ trans('global.action') }}
                                                    </a>
                                                
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <a href="{{ route('admin.loans.show',$loan->id) }}" class="dropdown-item">
                                                            <i class="fa fa-eye"></i> &nbsp; View
                                                        </a>

                                                        <a href="{{ route('admin.loans.edit',$loan->id) }}" class="dropdown-item">
                                                            <i class="fa fa-edit"></i> &nbsp; Edit
                                                        </a>

                                                        <form action="{{ route('admin.loans.destroy', $loan->id) }}" method="POST"
                                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="tab-pane fade" id="vacations" role="tabpanel" aria-labelledby="vacations-tab">
                            <table class="table table-striped table-hover table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>From : To</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee->vacations as $vacation)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $vacation->name }}</td>
                                            <td>{{ $vacation->description }}</td>
                                            <td>
                                                {{ $vacation->from .' : '.$vacation->to}} <br>
                                                <span class="badge badge-success">{{ $vacation->diff }} Day/s</span>
                                            </td>
                                            <td>{{ $vacation->created_by->name }}</td>
                                            <td>{{ $vacation->created_at }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                                                        aria-expanded="false">
                                                        {{ trans('global.action') }}
                                                    </a>
                                                
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <a href="{{ route('admin.vacations.show',$vacation->id) }}" class="dropdown-item">
                                                            <i class="fa fa-eye"></i> &nbsp; View
                                                        </a>

                                                        <a href="{{ route('admin.vacations.edit',$vacation->id) }}" class="dropdown-item">
                                                            <i class="fa fa-edit"></i> &nbsp; Edit
                                                        </a>

                                                        <form action="{{ route('admin.vacations.destroy', $vacation->id) }}" method="POST"
                                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                            <table class="table table-striped table-hover table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee->documents as $document)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($document->image)
                                                    <a href="{{ $document->image->getUrl() }}" 
                                                        style="display: inline-block">
                                                        <img src="{{ $document->image->getUrl() }}" class="rounded-circle"
                                                            style="width: 150px;height:150px">
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ $document->name }}</td>
                                            <td>{{ $document->description }}</td>
                                            <td>{{ $document->created_by->name }}</td>
                                            <td>{{ $document->created_at }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                                                        aria-expanded="false">
                                                        {{ trans('global.action') }}
                                                    </a>
                                                
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <a href="{{ route('admin.documents.show',$document->id) }}" class="dropdown-item">
                                                            <i class="fa fa-eye"></i> &nbsp; View
                                                        </a>

                                                        <a href="{{ route('admin.documents.edit',$document->id) }}" class="dropdown-item">
                                                            <i class="fa fa-edit"></i> &nbsp; Edit
                                                        </a>

                                                        <form action="{{ route('admin.documents.destroy', $document->id) }}" method="POST"
                                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
