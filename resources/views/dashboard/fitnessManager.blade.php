 <div class="card">
     <div class="card-header">
         <h5><i class="fa fa-user"></i> Monthly PT MemberShips</h5>
     </div>
     <div class="card-body">
         <div class="row">
             <div class="col-md-12">
                 <div class="table-responsive">
                     <table class="table table-striped table-hover table-bordered zero-configuration">
                         <thead>
                             <th>#</th>
                             <th>
                                 {{ trans('cruds.lead.fields.member_code') }}
                             </th>
                             <th>
                                 {{ trans('cruds.membership.fields.member') }}
                             </th>
                             {{-- <th>{{ trans('cruds.branch.title_singular') }}</th> --}}
                             <th>{{ trans('cruds.membership.title') }}</th>
                             <th>{{ trans('cruds.service.fields.service_type') }}</th>
                             <th>{{ trans('global.date') }}</th>
                             <th>{{ trans('global.trainer') }}</th>
                             <th>Remaining Sessions</th>
                             <th>Assign Date</th>
                             <th>{{ trans('global.created_at') }}</th>
                             <th>Action</th>
                         </thead>
                         <tbody>
                             @foreach ($pt_members as $value)
                                 <tr>
                                     <td>{{ $loop->iteration }}</td>
                                     <td>{{ $value->member->branch->member_prefix . '' . $value->member->member_code }}
                                     </td>
                                     <td>
                                         <a href="{{ route('admin.members.show', $value->member->id) }}">
                                             {{ $value->member->name }} <br>
                                             {{ $value->member->phone }} <br>
                                             {{-- Memberships : {{ $value->member->memberships_count }} <br> --}}

                                         </a>
                                     </td>
                                     {{-- <td>{{ $value->branch->name ?? '-' }}</td> --}}
                                     <td>
                                         {{ $value->service_pricelist->name ?? '-' }}
                                         <br>
                                         <span class="badge badge-{{ App\Models\Membership::STATUS[$value->status] }}">
                                             {{ App\Models\Membership::SELECT_STATUS[$value->status] }}
                                         </span>
                                     </td>
                                     <td>
                                         {{ $value->service_pricelist->service->service_type->name ?? '-' }}
                                     </td>
                                     <td>
                                         {{ 'Start Date : ' . $value->start_date ?? '-' }} <br>
                                         {{ 'End Date : ' . $value->end_date ?? '-' }}
                                     </td>
                                     <td>
                                         {{ $value->trainer->name ?? '-' }}
                                     </td>
                                     <td>

                                         {{ $value->attendances_count ?? 0 }} \
                                         {{ $value->service_pricelist->session_count ?? 0 }}
                                     </td>
                                     <td>
                                         {{ $value->assign_date ?? '-' }}
                                     </td>
                                     <td>
                                         {{ $value->created_at->toFormattedDateString() ?? '-' }}
                                     </td>
                                     <td>
                                         <div class="btn-group">
                                             @if ($value->status != 'expired')
                                                 <a href="{{ route('membership.get_details', ['membership_id' => $value->member->member_code]) }}"
                                                     class="btn btn-success btn-sm" target="_blank">
                                                     <i class="fas fa-fingerprint"></i> Attend
                                                 </a>
                                             @endif
                                             <a href="javascript:;" data-toggle="modal" data-target="#assignTrainer"
                                                 class="btn btn-info btn-sm"
                                                 onclick="assignTrainer({{ $value->id }})">
                                                 <i class="fa fa-exchange"></i> Assign Trainer
                                             </a>
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

 <div class="row">
     <div class="col-12">
         <div class="my-2">
             <label for="" class="form-label">Bulk Options</label>
             <div class="input-group col-6">
                 <select name="" class="form-control bulkOptionSelect " id="">
                     <option value="">Select Action</option>
                     <option value="assign_coach">Assign Coach</option>

                 </select>
                 <button type="submit" onclick="bulkOptionFunc()" class="btn  btn-primary">
                     Do
                 </button>
             </div>
         </div>
     </div>

     <div class="modal hidden" id="assignCoach" tabindex="-1">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">Assign Coach</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <form action="" method="post">
                         <input type="text" name="selectedMembers" class="selectedMembers" id="selectedMembers"
                             hidden>
                         @csrf
                         @method('POST')

                         <div class="row">
                             <div class="col-md-12">
                                 <label class="required"
                                     for="assigned_coach_id">{{ trans('cruds.member.fields.trainer') }}</label>
                                 <select
                                     class="form-control  {{ $errors->has('assigned_coach_id') ? 'is-invalid' : '' }}"
                                     name="assigned_coach_id" id="assigned_coach_id" required>
                                     <option disabled selected hidden>{{ trans('global.pleaseSelect') }}</option>
                                     @foreach ($coaches as $id => $entry)
                                         <option value="{{ $id }}"
                                             {{ old('assigned_coach_id') == $id ? 'selected' : '' }}>
                                             {{ $entry }}</option>
                                     @endforeach
                                 </select>
                                 @if ($errors->has('assigned_coach_id'))
                                     <div class="invalid-feedback">
                                         {{ $errors->first('assigned_coach_id') }}
                                     </div>
                                 @endif
                                 <span class="help-block">{{ trans('cruds.member.fields.trainer_helper') }}</span>
                             </div>
                         </div>

                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     <button type="submit" class="btn btn-primary">Save changes</button>
                 </div>
                 </form>
             </div>
         </div>
     </div>

     <div class="card">
         <div class="card-header">
             <h5><i class="fa fa-user"></i> Monthly NON-PT MemberShip</h5>
         </div>
         <div class="card-body">
             <div class="row">
                 <div class="col-md-12">
                     <div class="table-responsive">
                         <table class="table table-striped table-hover table-bordered zero-configuration ">
                             <thead>
                                 <tr>
                                     <th width="5%">
                                         <input type="checkbox" onchange="checkAllCheckBoxes(this)"
                                             class="block mx-auto" name="checkAll" id="checkAllCheckBoxes">
                                     </th>
                                     <th>#</th>
                                     <th>{{ trans('cruds.lead.fields.member_code') }}</th>
                                     <th>
                                         {{ trans('cruds.membership.fields.member') }}
                                     </th>

                                     {{-- <th>{{ trans('cruds.branch.title_singular') }}</th> --}}
                                     <th>{{ trans('cruds.membership.title') }}</th>
                                     <th>{{ trans('cruds.service.fields.service_type') }}</th>
                                     <th>{{ trans('global.date') }}</th>
                                     {{-- <th>{{ trans('global.trainer') }}</th> --}}
                                     <th>Status</th>
                                     <th>Assign Date</th>
                                     <th>{{ trans('global.created_at') }}</th>
                                     <th>Assigned Coach </th>
                                     <th>{{ trans('global.action') }}</th>
                                 </tr>

                             </thead>
                             <tbody>
                                 @foreach ($non_pt_members as $value)
                                     <tr>
                                         <td>
                                             {{-- @if ($value->assigned_coach_id != null)
                                                 <p class='text-center'>
                                                     &check;</p>
                                             @else --}}
                                             <input type="checkbox" value="{{ $value->id }}"
                                                 class="checkable block mx-auto checkable_item_{{ $value->id }}"
                                                 name="someCheckbox">
                                             {{-- @endif --}}
                                         </td>

                                         <td>{{ $loop->iteration }}</td>
                                         <td>{{ $value->member->branch->member_prefix . '' . $value->member->member_code }}
                                         </td>
                                         <td>
                                             <a href="{{ route('admin.members.show', $value->member->id) }}">
                                                 {{ $value->member->name }} <br>
                                                 {{ $value->member->phone }} <br>
                                                 {{ $value->member_id }}

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

                                                 {{ $value->membership_status }}
                                                </span>
                                         </td>
                                         <td>
                                            {{ $value->assign_date ?? '-' }}
                                         </td>
                                         <td>
                                             {{ $value->created_at->toFormattedDateString() ?? '-' }}
                                         </td>
                                         <td>

                                             {{ $value->assigned_coach->name ?? 'Not Assigned' }}
                                             {{-- {{ $value->assigned_coach->name ?? 'Not Assigned' }} --}}
                                         </td>
                                         <td>
                                             <div class="btn-group">
                                                 {{-- <form action="{{ route('attendance.take') }}" method="POST"
                                                    onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="hidden" name="membership_id" value="{{ $value->id }}">
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-fingerprint"></i> Attend
                                                    </button>
                                                </form> --}}
                                                 <a href="javascript:;" data-toggle="modal"
                                                     data-target="#assignTrainerNonPt" class="btn btn-info btn-sm"
                                                     onclick="assignTrainerNonPt({{ $value->id }})">
                                                     <i class="fa fa-exchange"></i> Assign Trainer
                                                 </a>
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

 <div class="card">
     <div class="card-header">
         <h5><i class="fa fa-user"></i> Trainers Payments</h5>
     </div>
     <div class="card-body">
         <div class="row">
             <div class="col-md-12">
                 <div class="table-responsive">
                     <table class="table table-striped table-hover table-bordered zero-configuration">
                         <thead>
                             <th>#</th>
                             <th>Trainer</th>
                             <th>Monthly Payments</th>
                             <td>Action</td>
                         </thead>
                         <tbody>
                             @foreach ($list as $key => $value)
                                 <tr>
                                     <td>{{ $loop->iteration }}</td>
                                     <td>
                                         {{ $value['trainer_name'] }}
                                     </td>

                                     <td>
                                         {{ number_format($value['payments_amount']) ?? '-' }}
                                     </td>
                                     <td>
                                         <a href="{{ route('admin.reports.index.trainer-payments', $value['id']) }}"
                                             class="btn btn-info">Show
                                         </a>
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


 <div class="card">
     <div class="card-header">
         <h5><i class="fa fa-user"></i> Trainers Reminders</h5>
     </div>
     <div class="card-body">
         <div class="row">
             <div class="col-md-12">
                 <div class="table-responsive">
                     <table class="table table-striped table-hover table-bordered zero-configuration">
                         <thead>
                             <th>#</th>

                             <th>Trainer</th>
                             <th style="background-color: red">Overdue Reminders</th>
                             <th style="background-color: rgb(93, 93, 7)">Today Reminders</th>
                             <th style="background-color: blue">UpComming Reminders</th>
                             {{-- <td>Action</td> --}}
                         </thead>
                         <tbody>
                             @foreach ($reminders as $key => $val)
                                 <tr>
                                     <td>{{ $loop->iteration }}</td>
                                     <td>
                                         {{ $val['name'] }}
                                     </td>

                                     <td>
                                         <a href="{{ route('admin.reminders.overdue', ['user_id[]' => $val['id']]) }}"
                                             class="text-decoration-none" target="_blank">

                                             {{ $val['overdue_reminders'] }}
                                         </a>
                                     </td>
                                     <td>
                                         <a href="{{ route('admin.reminders.index', ['user_id[]' => $val['id']]) }}"
                                             class="text-decoration-none" target="_blank">
                                             {{ $val['today_reminders'] }}
                                         </a>
                                     </td>
                                     <td>
                                         <a href="{{ route('admin.reminders.upcomming', [
                                             'due_date[from]' => date('Y-m-01'),
                                             'due_date[to]' => date('Y-m-t'),
                                             'user_id[]' => $val['id'],
                                         ]) }}"
                                             class="text-decoration-none" target="_blank">
                                             {{ $val['upcomming_reminders'] }}
                                         </a>
                                     </td>
                                     {{-- <td>
                                         <a href="{{ route('admin.reports.index.trainer-payments', $value['id']) }}"
                                             class="btn btn-info">Show</a>
                                     </td> --}}
                                 </tr>
                             @endforeach
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
     </div>
 </div>

 
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="fa fa-file"></i> {{ trans('global.offers_report') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th class="text-dark">{{ trans('cruds.service.fields.name') }}</th>
                                <th class="text-dark">{{ trans('global.count') }}</th>
                                <th class="text-dark">{{ trans('global.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($offers as $key => $offer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $key }}</td>
                                    <td>{{ $offer->count() }}</td>
                                    <td>{{ number_format($offer->sum('amount')) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Chart
            </div>
            <div class="card-body">
                <canvas id="myChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
</div>

 <div id="assignTrainer" class="modal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog">
         <form action="" method="post">
             @csrf
             @method('PUT')
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">Assign Trainer</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <input type="hidden" name="type" value="pt">
                     <div class="form-group">
                         <label for="to_trainer_id">To Trainer</label>
                         <select class="form-control" name="to_trainer_id">
                             <option value="{{ null }}">Trainer</option>
                             @foreach ($trainers as $trainer_id => $trainer_name)
                                 <option value="{{ $trainer_id }}">{{ $trainer_name }}</option>
                             @endforeach
                         </select>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                             class="fa fa-times-circle"></i> Close</button>
                     <button type="submit" class="btn btn-success">
                         <i class="fa fa-check-circle"></i>
                         Confirm
                     </button>
                 </div>
             </div>
         </form>
     </div>
 </div>

 <div id="assignTrainerNonPt" class="modal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog">
         <form action="" method="post">
             @csrf
             @method('PUT')
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">Assign Trainer</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <div class="form-group">
                         <input type="hidden" name="type" value="non_pt">
                         <label for="to_trainer_id">To Trainer</label>
                         <select class="form-control" name="to_trainer_id_non_pt">
                             <option value="{{ null }}">Trainer</option>
                             @foreach ($trainers as $trainer_id => $trainer_name)
                                 <option value="{{ $trainer_id }}">{{ $trainer_name }}</option>
                             @endforeach
                         </select>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                             class="fa fa-times-circle"></i> Close</button>
                     <button type="submit" class="btn btn-success">
                         <i class="fa fa-check-circle"></i>
                         Confirm
                     </button>
                 </div>
             </div>
         </form>
     </div>
 </div>



 <script>
     function assignCoach(id) {
         var id = id;
         var url = "{{ route('admin.assign-coach-to-membership.memberships', ':id') }}",
             url = url.replace(':id', id);
         $.ajax({
             method: 'GET',
             url: url,
             success: function(response) {
                 var route = "{{ route('admin.assign-coach.memberships', ':id') }}";
                 route = route.replace(':id', response.id);
                 $('form').attr('action', route)
             }
         });
     }

     function checkAllCheckBoxes(checkbox) {
         let tableCheckBoxes = document.querySelectorAll('.checkable');
         for (let i = 0; i < tableCheckBoxes.length; i++) {
             tableCheckBoxes[i].checked = checkbox.checked;
         }
     }

     //  function onModalBlur() {
     //      $(".selectedMembers").val('')
     //  }

     function bulkOptionFunc() {
         if (document.querySelectorAll('.checkable:checked').length > 0) {
             let bulkOptionValue = document.querySelector('.bulkOptionSelect').value;
             if (bulkOptionValue === 'assign_coach') {


                 let checkedMembers = [];
                 document.querySelectorAll('.checkable:checked').forEach((checkbox) => {
                     checkedMembers.push(checkbox.value);
                 });
                 $('#assignCoach').modal('show');
                 $(".selectedMembers").val(`${checkedMembers}`);
                 $('form').attr('action', "{{ route('admin.assign-coach.memberships') }}")
             }
         } else {
             alert('Please Select at Least One Member !');
         }

     }

     function assignTrainer(id) {
         var id = id;
         var url = "{{ route('admin.assign-coach-to-membership.memberships', ':id') }}",
             url = url.replace(':id', id);
         $.ajax({
             method: 'GET',
             url: url,
             success: function(response) {
                 var route = "{{ route('admin.assign-trainer', ':membership_id') }}";

                 route = route.replace(':membership_id', response.id);

                 //  alert(route);
                 $('form').attr('action', route)
             }
         });
     }

     function assignTrainerNonPt(id) {
         var id = id;
         var url = "{{ route('admin.assign-coach-to-membership.memberships', ':id') }}",
             url = url.replace(':id', id);
         $.ajax({
             method: 'GET',
             url: url,
             success: function(response) {
                 var route = "{{ route('admin.assign-trainer', ':membership_id') }}";

                 route = route.replace(':membership_id', response.id);

                 //  alert(route);
                 $('form').attr('action', route)
             }
         });
     }
 </script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [
                @php 
                    foreach($offers as $key => $offer){
                        echo "'".$key."'" . ',';
                    }
                @endphp
            ],
            datasets: [{
                label: '# of Votes',
                data: [@php 
                    foreach($offers as $key => $offer){
                        echo "'".$offer->sum('amount')."'" . ',';
                    }
                @endphp],
                
                backgroundColor: [
                    
                    @php 
                    function random_color_part() {
                        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
                    }

                    function random_color() {
                        return random_color_part() . random_color_part() . random_color_part();
                    }

                    foreach($offers as $key => $offer){
                           echo "'#" . random_color() . "',";
                        }
                    @endphp
                ],
                
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>