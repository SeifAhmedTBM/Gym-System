<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Branch;
use App\Models\Locker;
use App\Models\Setting;
use App\Models\Schedule;
use App\Models\Pricelist;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Models\TrainerAttendant;
use App\Http\Controllers\Controller;
use App\Models\MembershipAttendance;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use phpDocumentor\Reflection\Types\Null_;
use App\Exports\MembershipAttendanceExport;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\StoreMembershipAttendanceRequest;
use Carbon\Carbon;
use App\Http\Requests\MassDestroyMembershipAttendanceRequest;

class MembershipAttendanceController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        

        abort_if(Gate::denies('membership_attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','_']);

        $data['created_at']['from'] = isset($data['created_at']['from']) ? $data['created_at']['from'] : $startOfMonth;
        $data['created_at']['to'] = isset($data['created_at']['to']) ? $data['created_at']['to'] : $endOfMonth;

        $settings = Setting::first();

        $employee = Auth()->user()->employee;

        if ($request->ajax()) {
            if ($employee && $employee->branch_id != NULL)
            {
                $query = MembershipAttendance::index($data)->whereHas('membership',function($q) use ($employee){
                                                    $q->whereHas('member',function($y) use ($employee){
                                                        $y->whereBranchId($employee->branch_id);
                                                    });
                                                })
                                                ->with(['membership','membership.member','membership.service_pricelist'])
                                                ->latest()->select(sprintf('%s.*', (new MembershipAttendance())->table));
            }else{
                $query = MembershipAttendance::index($data)->whereHas('membership')
                                                ->with(['membership','membership.member','membership.service_pricelist'])
                                                ->latest()->select(sprintf('%s.*', (new MembershipAttendance())->table));
            }
            
            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                    $viewGate = 'membership_attendance_show';
                    $editGate = 'membership_attendance_edit';
                    $deleteGate = 'membership_attendance_delete';
                    $crudRoutePart = 'membership-attendances';

                    return view('partials.datatablesActions', compact(
                    'viewGate',
                    // 'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });

            $table->addColumn('trainer', function ($row) {
                return $row->membership->trainer ? $row->membership->trainer->name : '-';
            });

            $table->addColumn('status', function ($row) {
                return $row->membership_status ? '<span
                class="badge badge-'.Membership::STATUS[$row->membership_status].' p-2">
                <i class="fa fa-recycle"></i> '. ucfirst($row->membership_status) .'
            </span>' : '-';
            });

            $table->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at : '';
            });

            $table->editColumn('membership_member', function ($row) use($settings){
                return $row->id ? '<a href="'.route('admin.members.show',$row->membership->member->id).'" target="_blank">' . $settings->member_prefix . $row->membership->member->member_code . '<br />' . $row->membership->member->name. '<br / >' .$row->membership->member->phone . '</a>' : '';
            });

            $table->addColumn('membership', function ($row) {
                return $row->membership && $row->membership->service_pricelist ?  '<a href="'.route('admin.memberships.show',$row->membership_id).'" target="_blank">'.$row->membership->service_pricelist->name.'</a>' : '';
            });

            $table->editColumn('sign_in', function ($row) {
                return $row->sign_in ? date('g:i A', strtotime($row->sign_in)) : '';
            });

            $table->editColumn('sign_out', function ($row) {
                return $row->sign_out ? date('g:i A', strtotime($row->sign_out)) : '-';
            });

            $table->editColumn('locker', function ($row) {
                return $row->locker ? $row->locker : '-';
            });

            $table->editColumn('branch_name', function ($row) {
                return $row->branch ? $row->branch->name : '-';
            });
            
            $table->addColumn('membership_id', function ($row) {
                return $row->membership ? $row->membership->id : '';
            });
            
            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions','created_at','trainer','status','placeholder','locker','membership','membership_member','branch_name']);

            return $table->make(true);
        }

        $service_pricelists = Pricelist::pluck('name','name');

        $branches = Branch::pluck('name','name');

        $trainers = User::whereRelation('roles', 'title', 'Trainer')->pluck('name', 'id');

        if ($employee && $employee->branch_id != NULL)
        {
            $counter = MembershipAttendance::index($data)
                                            ->whereHas('membership',function($q) use ($employee){
                                                $q->whereHas('member',function($y) use ($employee){
                                                    $y->whereBranchId($employee->branch_id);
                                                });
                                            })
                                            ->with(['membership','membership.member'])
                                            ->count();
        }else{
            $counter = MembershipAttendance::index($data)
                                            ->whereHas('membership')
                                            ->with(['membership','membership.member'])
                                            ->count();
        }

        return view('admin.membershipAttendances.index', compact('trainers','counter','service_pricelists','branches'));
    }

    public function create()
    {
        abort_if(Gate::denies('membership_attendance_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $memberships = Membership::pluck('member_id', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.membershipAttendances.create', compact('memberships'));
    }

    public function store(StoreMembershipAttendanceRequest $request)
    {
        $membershipAttendance = MembershipAttendance::create($request->all());

        return redirect()->route('admin.membership-attendances.index');
    }

    public function edit(MembershipAttendance $membershipAttendance)
    {
        return response()->json([
            'membership_attendances'    => $membershipAttendance,
            'sign_in'                   => date('H:i',strtotime($membershipAttendance->sign_in)),
            'sign_out'                  => date('H:i',strtotime($membershipAttendance->sign_out)),
        ]);
    }

    public function update(UpdateMembershipAttendanceRequest $request, MembershipAttendance $membershipAttendance)
    {
        $membershipAttendance->update($request->all());
        
        $this->updated();
        return back();
    }

    public function show(MembershipAttendance $membershipAttendance)
    {
        abort_if(Gate::denies('membership_attendance_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.membershipAttendances.show', compact('membershipAttendance'));
    }

    public function destroy(MembershipAttendance $membershipAttendance)
    {
        abort_if(Gate::denies('membership_attendance_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $trainer_id = $membershipAttendance->membership->trainer_id;
        $member_id = $membershipAttendance->membership->member_id;
        $trainerAttendant = TrainerAttendant::where('member_id', $member_id)->where('trainer_id', $trainer_id)->where('created_at', $membershipAttendance->created_at)->first();
        if($trainerAttendant) {
            $trainerAttendant->delete();
        }
        $membershipAttendance->delete();

        return back();
    }

    public function massDestroy(MassDestroyMembershipAttendanceRequest $request)
    {
        MembershipAttendance::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function export(Request $request)
    {
        return Excel::download(new MembershipAttendanceExport($request), 'Membership-Attendances.xlsx');
    }
}
