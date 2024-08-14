<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lead;
use App\Models\Schedule;
use App\Models\Membership;
use App\Models\ScheduleMain;
use Illuminate\Http\Request;
use App\Models\TrainerAttendant;
// use Response;
use App\Models\MembershipSchedule;
use App\Http\Controllers\Controller;
use App\Models\TrainerSessionAttendance;
use Facade\FlareClient\Http\Response;
use Yajra\DataTables\Facades\DataTables;
// use Symfony\Component\HttpFoundation\Response;

class MembershipScheduleController extends Controller
{
    public function index($id)
    {

        $schedule = Schedule::with([
            'schedule_main',
            'schedule_main.membership_schedules' => fn ($q) => $q->whereIsActive('active'),
            'schedule_main.membership_schedules.membership' => fn ($q) => $q->withCount('trainer_attendances'),
            'schedule_main.membership_schedules.membership.member',
            'schedule_main.membership_schedules.membership.service_pricelist'
        ])->findOrFail($id);

        return view('admin.membership_schedule.index', compact('schedule'));
    }

    public function create($id)
    {
        $schedule = Schedule::with('schedule_main')->findOrFail($id);

        $memberships = Membership::whereDoesnthave('membership_schedules', function ($q) use ($schedule) {
            $q->where('schedule_main_id', $schedule->schedule_main_id);
        })
            ->whereIn('status', ['current', 'expiring'])
            ->latest()
            ->get();

        return view('admin.membership_schedule.create', compact('schedule', 'memberships'));
    }

    public function attendances(Request $request, $id)
    {
        $schedule = Schedule::with([
            'schedule_main',
            'trainer_attendants',
            'schedule_main.membership_schedules' => fn ($q) => $q->whereIsActive('active'),
        ])->findOrFail($id);

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);

        $from = $request['from'] ?? date('Y-m-01');
        $to = $request['to'] ?? date('Y-m-t');


        if ($request->ajax()) {
            $query = TrainerAttendant::with([
                'membership' => fn ($q) => $q->withCount('trainer_attendances'),
                'membership.member',
                'membership.member.branch',
                'membership.service_pricelist',
            ])
                // ->whereHas('membership',fn($q) => $q->whereHas('membership_schedules',fn($y) => $y->whereIsActive('active')))
                ->whereScheduleId($schedule->id)
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to);

            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = '';
                $editGate = '';
                $deleteGate = 'membership_delete';
                $crudRoutePart = 'trainer-attendances';

                return view('partials.datatablesActions', compact(
                    // 'viewGate',
                    // 'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });

            $table->addColumn('membership_name', function ($row) {
                return $row->membership && $row->membership->service_pricelist ? $row->membership->service_pricelist->name : '';
            });

            $table->addColumn('start_date', function ($row) {
                return $row->membership ? $row->membership->start_date : '';
            });

            $table->addColumn('end_date', function ($row) {
                return $row->membership ? $row->membership->end_date : '';
            });

            $table->addColumn('sessions', function ($row) {
                return $row->membership ? $row->membership->trainer_attendances_count . ' / ' . $row->membership->service_pricelist->session_count : '';
            });

            $table->addColumn('member_name', function ($row) {
                return $row->member->name ? $row->member->branch->member_prefix . $row->member->member_code . '<br>' . '<a href="' . route('admin.members.show', $row->member->id) . '" target="_blank">' . $row->member->name . '</a>'
                    . '<br/>' . '<b>' . $row->member->phone . '<b>'
                    . '<br/>' : '';
            });

            $table->addColumn('membership_status', function ($row) {
                return $row->membership ? $row->membership->status : '';
            });

            $table->addColumn('created_at', function ($row) {
                return $row->created_at ? date('Y-m-d', strtotime($row->created_at)) : '';
            });

            $table->rawColumns(['actions', 'created_at', 'placeholder', 'membership_name', 'member_name', 'start_date', 'end_date', 'sessions', 'membership_status']);

            return $table->make(true);
        }

        return view('admin.membership_schedule.attendances', compact('schedule'));
    }

    public function showAttendanceDetails(Request $request, $id)
    {
        $membershipSchedule = MembershipSchedule::with(['membership'])->find($id);

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);

        if ($request->ajax()) {
            $query = TrainerAttendant::with('membership')->where('membership_id', $membershipSchedule->membership_id);


            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = '';
                $editGate = '';
                $deleteGate = 'membership_delete';
                $crudRoutePart = 'trainer-attendances';

                return view('partials.datatablesActions', compact(
                    // 'viewGate',
                    // 'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });

            $table->addColumn('created_at', function ($row) {
                return $row->created_at ? date('Y-m-d', strtotime($row->created_at)) : '';
            });

            $table->rawColumns(['actions', 'created_at', 'placeholder']);

            return $table->make(true);
        }


        // return view('admin.membershipAttendances.index', compact('trainers','counter','service_pricelists','branches'));
        return view('admin.membership_schedule.details', compact('membershipSchedule'));
    }

    // public function deleteAttendannce($id){
    //     TrainerAttendant::findOrFail($id)->delete();

    //     return back();
    // }

    // public function deletePluckAttendannce(Request $request)
    // {
    //     TrainerAttendant::whereIn('id', request('ids'))->delete();

    //     return response(null, Response::HTTP_NO_CONTENT);
    // }

    public function store(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        MembershipSchedule::create([
            'membership_id'         => $request['membership_id'],
            'schedule_main_id'      => $schedule->schedule_main_id,
            'schedule_id'           => $schedule->id
        ]);

        $this->sent_successfully();
        return redirect()->route('admin.membership-schedule.index', $schedule->id);
    }


    public function destroy($id)
    {
        MembershipSchedule::findOrFail($id)->delete();

        $this->sent_successfully();
        return back();
    }

    public function swip_attend(Request $request, $id)
    {
        // Must Revised 
        if ($request['card_number']) {
            $member = Lead::whereCardNumber($request['card_number'])->first();

            $membership_schedule = MembershipSchedule::whereIn('membership_id', $member->memberships->pluck('id')->toArray())->first();

            $membership = $membership_schedule->membership;

            $trainer_attendances_count = $membership->trainer_attendances()->count();

            $total_count =  $membership->service_pricelist->session_count;

            if ($trainer_attendances_count < $total_count) {
                $attend = TrainerAttendant::create([
                    'member_id'         => $member->id,
                    'membership_id'     => $membership->id,
                    'trainer_id'        => $request['trainer_id'],
                    'schedule_id'       => $request['schedule_id'],
                ]);

                $membership->update([
                    'last_attendance'      => $attend->created_at
                ]);

                $this->sent_successfully();
            } else {
                $this->something_wrong();
            }
        }

        return back();
    }

    public function attend_membership(Request $request)
    {


        try {
            $check = TrainerSessionAttendance::where('trainer_id', $request['trainer_id'])
                ->where('schedule_id', $request['schedule_id'])
                ->whereDate('date', date('Y-m-d'))
                ->get();

            if ($check->count() == 0) {

                $attendace =   TrainerSessionAttendance::create([
                    'trainer_id'        => $request['trainer_id'],
                    'schedule_id'       => $request['schedule_id'],
                    'date'              => date('Y-m-d'),
                    'created_at'        => date('Y-m-d')
                ]);
            }

            $membership = Membership::find($request['membership_id']);

            $trainer_attendances_count = $membership->trainer_attendances()->count();



            $total_count =  $membership->service_pricelist->session_count;

            if ($trainer_attendances_count < $total_count) {
                $attend = TrainerAttendant::create([
                    'member_id'         => $request['member_id'],
                    'membership_id'     => $request['membership_id'],
                    'trainer_id'        => $request['trainer_id'],
                    'schedule_id'       => $request['schedule_id'],
                ]);

                $membership->update([
                    'last_attendance'      => $attend->created_at
                ]);
                // dd('hello');
                return  response()->json('Saved Successfully', 200);
            } else {
                return Response()->json('Error data not saved', 400);
            }
        } catch (\Throwable $th) {
            return Response()->json($th, 400);
        }
    }
}
