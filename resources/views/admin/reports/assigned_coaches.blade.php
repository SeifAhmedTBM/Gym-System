   @extends('layouts.admin')
   @section('content')
       <div class="card">
           <div class="card-body">
               <form action="{{ route('admin.reports.assigned-coaches.report') }}" method="get">
                   <div class="row">
                       <div class="col-md-8">

                           <div class="input-group">
                            
                               <select name="branch_id" id="branch_id" class="form-control"
                                   >
                                   <option value="{{ null }}" selected hidden disabled>Branch</option>
                                   @foreach (\App\Models\Branch::pluck('name', 'id') as $id => $name)
                                       <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>
                                           {{ $name }}</option>
                                   @endforeach
                               </select>
                               <div class="input-group-prepend">
                                   <button class="btn btn-primary" type="submit">{{ trans('global.submit') }}</button>
                               </div>
                           </div>
                       </div>
                   </div>
               </form>
           </div>
       </div>


       <div class="card">
           <div class="card-header">
               <h5><i class="fa fa-user"></i> Monthly Assigned Members</h5>
           </div>
           <div class="card-body">
               <div class="row">
                   <div class="col-md-12">
                       <div class="table-responsive">
                           <table class="table table-striped table-hover table-bordered ">
                               <thead>
                                   <tr>

                                       <th>#</th>
                                       <th>
                                           {{ trans('cruds.membership.fields.member') }}
                                       </th>
                                       {{-- <th>{{ trans('cruds.branch.title_singular') }}</th> --}}
                                       <th>{{ trans('cruds.membership.title') }}</th>
                                       <th>{{ trans('cruds.service.fields.service_type') }}</th>
                                       <th>{{ trans('global.date') }}</th>
                                       {{-- <th>{{ trans('global.trainer') }}</th> --}}
                                       <th>Status</th>
                                       <th>{{ trans('global.created_at') }}</th>
                                       <th>Assigned Coach </th>
                                       {{-- <th>{{ trans('global.action') }}</th> --}}
                                   </tr>

                               </thead>
                               <tbody>
                                   @foreach ($non_pt_members as $value)
                                       <tr>


                                           <td>{{ $loop->iteration }}</td>
                                           <td>
                                               <a href="{{ route('admin.members.show', $value->member->id) }}">
                                                   {{ $value->member->name }} <br>
                                                   {{ $value->member->phone }} <br>
                                                   {{ $value->member->branch->member_prefix . '' . $value->member->member_code }}
                                                   {{-- Memberships : {{ $value->member->memberships_count }} --}}
                                               </a>
                                           </td>
                                           {{-- <td>{{ $value->branch->name ?? '-' }}</td> --}}
                                           <td>
                                               {{ $value->service_pricelist->name ?? '-' }}
                                           </td>
                                           <td>
                                               {{ $value->service_pricelist->service->service_type->name ?? '-' }}
                                           </td>
                                           <td>
                                               {{ 'Start Date : ' . $value->start_date ?? '-' }} <br>
                                               {{ 'End Date : ' . $value->end_date ?? '-' }}
                                           </td>
                                           {{-- <td>
                                         {{ $value->trainer->name ?? 'Assign One' }}
                                     </td> --}}
                                           <td>
                                               <span
                                                   class='badge badge-{{ App\Models\Membership::MEMBERSHIP_STATUS_COLOR[$value->membership_status] }} " p-2'>

                                                   {{ $value->membership_status }}</span>
                                           </td>

                                           <td>
                                               {{ $value->created_at->toFormattedDateString() ?? '-' }}
                                           </td>
                                           <td>

                                               {{ $value->assigned_coach->name ?? '-' }}
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
   @endsection
