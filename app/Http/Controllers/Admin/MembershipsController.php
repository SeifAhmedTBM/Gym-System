<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\User;
use App\Models\Sport;
use App\Models\Branch;
use App\Models\Locker;
use App\Models\Source;
use App\Models\Status;
use App\Models\Account;
use App\Models\Address;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Reminder;
use App\Models\Pricelist;
use App\Models\Membership;
use App\Models\ServiceType;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\MemberStatus;
use App\Models\ScheduleMain;
use Illuminate\Http\Request;
use App\Models\FreezeRequest;
use App\Models\MemberReminder;
use App\Models\ExternalPayment;
use App\Models\TrackMembership;
use App\Exports\MembershipsExport;
use App\Models\MembershipSchedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\LeadRemindersHistory;
use App\Models\MembershipAttendance;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpiringExpiredExport;
use App\Models\MemberRemindersHistory;
use App\Models\MembershipServiceOptions;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\ExpiredMembershipsExport;
use App\Exports\ExpiringMembershipsExport;
use App\Exports\PtExpiredMembershipsExport;
use App\Exports\MainExpiredMembershipsExport;
use App\Http\Requests\StoreMembershipRequest;
use App\Exports\ExpiredMembershipsExtraExport;
use App\Http\Requests\UpdateMembershipRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyMembershipRequest;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Price;

class MembershipsController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('membership_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);

        // $counter = Membership::index($data)->get()->count();

        $employee = Auth()->user()->employee;

        $user = Auth()->user();

        if ($request->ajax()) {
            if ($employee && $employee->branch_id != NULL) {
                if ($user->roles[0]->title == 'Sales') 
                {
                    $query = Membership::index($data)
                        ->whereHas('member', function ($q) use ($employee) {
                            $q->whereBranchId($employee->branch_id);
                        })
                        ->with(['trainer', 'sales_by', 'service_pricelist.service.service_type', 'member.branch', 'sport'])
                        ->whereSalesById($user->id)
                        ->withCount('attendances')
                        ->withCount('trainer_attendances')
                        ->latest();

                }elseif($user->roles[0]->title == 'Trainer'){
                    $query = Membership::index($data)
                            ->whereHas('member', function ($q) use ($employee) {
                                $q->whereBranchId($employee->branch_id);
                            })
                            ->with(['trainer', 'sales_by', 'service_pricelist.service.service_type', 'member.branch', 'sport'])
                            ->whereTrainerId($user->id)
                            ->withCount('attendances')
                            ->withCount('trainer_attendances')
                            ->latest();
                }else {
                    $query = Membership::index($data)
                        ->whereHas('member', function ($q) use ($employee) {
                            $q->whereBranchId($employee->branch_id);
                        })
                        ->with(['trainer', 'sales_by','service_pricelist.service.service_type', 'member.branch', 'sport'])
                        ->withCount('attendances')
                        ->withCount('trainer_attendances')
                        ->latest();
                        // ->select(sprintf('%s.*', (new Membership())->table));
                }
            } else {
                $query = Membership::index($data)
                    ->whereHas('member')
                    ->with(['trainer', 'sales_by','service_pricelist.service.service_type', 'member.branch', 'sport'])
                    ->withCount('attendances')
                    ->withCount('trainer_attendances')
                    ->latest();
                    // ->select(sprintf('%s.*', (new Membership())->table));
            }

            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            // $counter = $query->get()->count();

            $table->editColumn('actions', function ($row) {
                $viewGate = 'membership_show';
                $editGate = 'membership_edit';
                $deleteGate = 'membership_dele';
                $crudRoutePart = 'memberships';
                // $last_attendance = MembershipAttendance::whereMembershipId($row->id)->whereSignOut(NULL)->latest()->first();

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row',
                // 'last_attendance'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });

            $table->addColumn('member_code', function ($row) {
                return $row->member ? $row->member->branch->member_prefix . $row->member->member_code : '';
            });

            $table->addColumn('member_gender', function ($row) {
                return $row->member ? Str::ucfirst($row->member->gender) : '';
            });

            $table->addColumn('member_name', function ($row) {
                return $row->member->name ? $row->member->branch->member_prefix . $row->member->member_code . '<br>' . '<a href="' . route('admin.members.show', $row->member->id) . '" target="_blank">' . $row->member->name . '</a>'
                    . '<br/>' . '<b>' . $row->member->phone . '<b>'
                    . '<br/>' . '<b>' . Lead::GENDER_SELECT[$row->member->gender] . '</b>' : '';
                // return $row->member ? $row->member->name : '';
            });

            $table->addColumn('member_phone', function ($row) {
                return $row->member ? '<a href="' . route('admin.members.show', $row->member_id) . '" target="_blank">' . $row->member->phone . '</a>' : '';
                // return $row->member ? $row->member->name : '';
            });

            $table->addColumn('remaining_sessions', function ($row) {
                return $row->member && $row->service_pricelist && $row->service_pricelist->service && $row->service_pricelist->service->service_type && $row->service_pricelist->service->service_type->session_type == 'non_sessions' ? ($row->attendances_count . ' \\ ' . $row->service_pricelist->session_count) : ($row->trainer_attendances_count . ' \\ ' . $row->service_pricelist->session_count);
            });

            $table->addColumn('trainer_name', function ($row) {
                return $row->trainer ? $row->trainer->name : '';
            });

            $table->addColumn('status', function ($row) {
                return $row->status ? "<span class='badge badge-" . Membership::MEMBERSHIP_STATUS_COLOR[$row->membership_status] . " p-2'>" .
                    $row->membership_status . "</span>" . '<br>' .
                    "<span class='font-weight-bold p-2 badge badge-" . Membership::STATUS[$row->status] . "'>"
                    . ucfirst(Membership::SELECT_STATUS[$row->status]) . "</span>" : '';
            });

            $table->addColumn('service_pricelist_name', function ($row) {
                return $row->service_pricelist ? $row->service_pricelist->name . '<br>' . '<span class="badge badge-info">' . $row->service_pricelist->service->service_type->name . '</span>' : '';
            });

            $table->addColumn('sales_by_name', function ($row) {
                return $row->sales_by ? $row->sales_by->name : '';
            });

            $table->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at : '';
            });

            $table->editColumn('sport', function ($row) {
                return $row->sport_id != NULL ? $row->sport->name : '----';
            });

            $table->addColumn('branch_name', function ($row) {
                return $row->member && $row->member->branch ? $row->member->branch->name : '-';
            });

            $table->editColumn('last_attendance', function ($row) {
                return $row->last_attendance ? $row->last_attendance : 'No Attendance';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'status', 'member', 'trainer', 'service_pricelist', 'sales_by', 'member_name', 'member_phone', 'member_code', 'service_pricelist_name', 'branch_name']);

            return $table->make(true);
        }


        $locker_statuses = [
            'nottaken' => 'Not taken',
            'taken' => 'Taken',
            'returned' => 'Returned'
        ];

        $lockers = Locker::pluck('code', 'id');

        $sales = User::whereRelation('roles','title','Sales')
                        ->whereHas('employee',fn($q) => $q->whereStatus('active'))
                        ->orderBy('name')
                        ->pluck('name','id');

        $services = Pricelist::pluck('name', 'id');

        $service_types = ServiceType::pluck('name', 'id');

        $trainers = User::whereRelation('roles','title','Trainer')
                            ->whereHas('employee',fn($q) => $q->whereStatus('active'))
                            ->orderBy('name')->pluck('name','id');

        $members = Lead::whereType('member')->pluck('name', 'id');

        $branches = Branch::pluck('name', 'id');

        // if ($employee && $employee->branch_id != NULL) {
        //     if ($user->roles[0]->title == 'Sales') 
        //     {
        //         $memberships_count = Membership::index($data)
        //             ->whereHas('member', function ($q) use ($employee) {
        //                 $q->whereBranchId($employee->branch_id);
        //             })
        //             ->whereSalesById($user->id)
        //             ->count();
        //     }elseif($user->roles[0]->title == 'Trainer'){
        //         $memberships_count = Membership::index($data)
        //                 ->whereHas('member', function ($q) use ($employee) {
        //                     $q->whereBranchId($employee->branch_id);
        //                 })
        //                 ->whereTrainerId($user->id)
        //                 ->count();
        //     }else {
        //         $memberships_count = Membership::index($data)
        //             ->whereHas('member', function ($q) use ($employee) {
        //                 $q->whereBranchId($employee->branch_id);
        //             })
        //             ->count();
        //         // ->select(sprintf('%s.*', (new Membership())->table));
        //     }
        // } else {
        //     $memberships_count = Membership::index($data)
        //         ->whereHas('member')
        //         ->count();
        //     // ->select(sprintf('%s.*', (new Membership())->table));
        // }

        return view('admin.memberships.index', compact('members', 'locker_statuses', 'lockers', 'sales', 'services', 'trainers', 'service_types', 'branches'));
    }

    public function adjust($id)
    {
        $membership = Membership::find($id);
        $this->adjustMembership($membership);
    }

    public function create()
    {
        abort_if(Gate::denies('membership_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $members = Lead::get(['id', 'name', 'member_code']);

        $trainers = User::whereHas('roles', function ($q) {
            $q->where('title', 'Trainer');
        })->whereHas('employee', function ($i) {
            $i->whereStatus('active');
        })->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $pricelists = Pricelist::whereStatus('active')->with(['service'])->latest()->get();

        $sales_bies = User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->whereHas('employee', function ($i) {
            $i->whereStatus('active');
        })->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');


        $last_invoice = Invoice::latest()->first()->id ?? 0;

        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $selected_branch = Auth()->user()->employee->branch ?? NULL;

        $accounts = Account::pluck('name', 'id');

        $memberStatuses = MemberStatus::pluck('name', 'id');

        $setting = Setting::first();

        $sports = Sport::pluck('name', 'id');

        $main_schedules = ScheduleMain::with(['session', 'trainer'])
            ->whereStatus('active')
            ->whereHas('schedule_main_group', fn($q) => $q->whereStatus('active'))
            ->latest()
            ->get();

        return view('admin.memberships.create', compact('members', 'trainers', 'pricelists', 'sales_bies', 'last_invoice', 'accounts', 'memberStatuses', 'setting', 'sports', 'branches', 'selected_branch', 'main_schedules'));
    }

    public function store(StoreMembershipRequest $request)
    {
        try {
            DB::beginTransaction();
            $member = Lead::find($request->member_id);
            $sales_by = $member->sales_by_id;
            $service_type_id = Pricelist::find($request->service_pricelist_id)->service->service_type_id;
            $current_memberships = Membership::where('member_id', $request->member_id)
                ->whereHas('service_pricelist.service.service_type', fn($q) => $q->where('id', $service_type_id))
                ->whereStatus('expired')
                ->get();

            foreach ($current_memberships as $membershit) {
                $membershit->update(['is_changed' => 1]);
            }

            $membership = Membership::create([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'member_id' => $request->member_id,
                'trainer_id' => $request->trainer_id,
                'service_pricelist_id' => $request->service_pricelist_id,
                'notes' => $request->notes,
                'sales_by_id' => $sales_by,
                'membership_status' => 'renew',
                'notes' => $request->notes,
                'sales_by_id' => $sales_by,
                'sport_id' => $request->sport_id ?? null,
                'is_changed' => 0
            ]);

            $trackMembership = TrackMembership::create([
                'membership_id' => $membership->id,
                'status' => 'new'
            ]);

            $invoice = Invoice::create([
                'discount'          => $request->discount_amount,
                'discount_notes'    => $request->discount_notes,
                'service_fee'       => $request->membership_fee,
                'net_amount'        => $request->membership_fee - $request->discount_amount,
                'membership_id'     => $membership->id,
                'branch_id'         => $request['branch_id'],
                'sales_by_id'       => $sales_by,
                'created_by_id'     => Auth()->user()->id,
                'status'            => ($request->membership_fee - $request->discount_amount) == $request->received_amount ? 'fullpayment' : 'partial',
                'created_at' => $request['created_at'] . date('H:i:s')
            ]);


            // Venom System
            if ($request['main_schedule_id'] != NULL) {
                foreach ($request['main_schedule_id'] as $key => $main_request) {
                    $main_schedule = ScheduleMain::with(['schedules'])->find($main_request);

                    foreach ($member->memberships as $key => $membership) {
                        if ($membership->status == 'expired') {
                            $membership_schedules = $membership->membership_schedules;
                            foreach ($membership_schedules as $membership_schedule) {
                                $membership_schedule->update([
                                    'is_active' => 'inactive'
                                ]);
                            }
                        }
                    }

                    MembershipSchedule::create([
                        'membership_id' => $membership->id,
                        'schedule_main_id' => $main_schedule->id,
                        'schedule_id' => $main_schedule->schedules->last()->id
                    ]);
                }
            }

            foreach ($request['account_amount'] as $key => $account_amount) {
                if ($account_amount > 0) {
                    $payment = Payment::create([
                        'account_id' => $request->account_ids[$key],
                        'amount' => $account_amount,
                        'invoice_id' => $invoice->id,
                        'sales_by_id' => $sales_by,
                        'created_by_id' => Auth()->user()->id,
                        'created_at' => $request['created_at'] . date('H:i:s')
                    ]);

                    $payment->account->balance = $payment->account->balance + $payment->amount;
                    $payment->account->save();

                    $transaction = Transaction::create([
                        'transactionable_type' => 'App\\Models\\Payment',
                        'transactionable_id' => $payment->id,
                        'amount' => $account_amount,
                        'account_id' => $request->account_ids[$key],
                        'created_by' => auth()->user()->id,
                        'created_at' => $request['created_at'] . date('H:i:s')
                    ]);
                }
            }

            if ($invoice->status == 'partial') {
                Reminder::create([
                    'type' => 'due_payment',
                    'membership_id' => $membership->id,
                    'lead_id' => $member->id,
                    'due_date' => $request->due_date != NULL ? $request->due_date : date('Y-m-d', strtotime('+3 Days')),
                    'user_id' => $sales_by,
                ]);

                $member->update([
                    'status_id' => Status::firstOrCreate(
                        ['name' => 'Debt Member'],
                        ['color' => 'warning', 'default_next_followup_days' => 1, 'need_followup' => 'yes']
                    )->id
                ]);
            } else {
                $member->update(['status_id' => Lead::find($request->member_id)->status_id]);
            }

            // Upgrade Reminder
            // Reminder::create([
            //     'type'              => 'upgrade',
            //     'membership_id'     => $membership->id,
            //     'lead_id'           => $member->id,
            //     'due_date'          => date('Y-m-d', strtotime($membership->start_date.'+'.$membership->service_pricelist->upgrade_date.'Days')),
            //     'user_id'           => $sales_by,
            // ]);

            // Follow up Reminder
            Reminder::create([
                'type' => 'follow_up',
                'membership_id' => $membership->id,
                'lead_id' => $member->id,
                'due_date' => date('Y-m-d', strtotime($membership->start_date . '+' . $membership->service_pricelist->followup_date . 'Days')),
                'user_id' => $sales_by,
            ]);

            // Renew Reminders
            $this->renew_call($membership);

            DB::commit();
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
        }

        $this->created();

        return redirect()->route('admin.invoices.show', $invoice->id);
    }

    public function edit(Membership $membership)
    {
        abort_if(Gate::denies('membership_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $members = Lead::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $trainers = User::whereHas('roles', function ($q) {
            $q->where('title', 'Trainer');
        })->whereHas('employee', function ($i) {
            $i->whereStatus('active');
        })->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $service_pricelist = Pricelist::whereStatus('active')->with('service')->get()->prepend(trans('global.pleaseSelect'), '');

        $sales_bies = User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->whereHas('employee', function ($i) {
            $i->whereStatus('active');
        })->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $membership->load('member', 'trainer', 'sales_by');

        $services = Service::pluck('name', 'id');

        return view('admin.memberships.edit', compact('members', 'trainers', 'service_pricelist', 'sales_bies', 'membership', 'services'));
    }

    public function update(UpdateMembershipRequest $request, $id)
    {
        $membership = Membership::findOrFail($id);

        $membership->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'trainer_id' => $request->trainer_id,
            'member_id' => $membership->member_id,
            'notes' => $request->notes,
            'created_at' => $request->created_at
        ]);


        // Renew Reminders
        $this->renew_call($membership);

        // Welcome call Reminders
        $this->welcome_call($membership);

        // From controller.php
        $this->adjustMembership($membership);

        return redirect()->route('admin.memberships.index');
    }

    public function show(Membership $membership)
    {
        abort_if(Gate::denies('membership_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $membership->load(['member', 'trainer', 'service_pricelist', 'sales_by', 'attendances'])->loadCount(['membership_service_options', 'attendances']);

        return view('admin.memberships.show', compact('membership'));
    }

    public function manualAttend(Membership $membership)
    {
        $membership->load([
            'member',
            'trainer',
            'service_pricelist',
            'sales_by',
            'attendances' => fn($q) => $q->latest()
        ])->loadCount(['membership_service_options', 'attendances','trainer_attendances']);

        return view('admin.memberships.manual_attend', compact('membership'));
    }

    public function destroy(Membership $membership)
    {
        abort_if(Gate::denies('membership_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $membership->load('payments', 'invoice');

        foreach ($membership->payments as $key => $payment) {
            $payment->account->balance -= $payment->amount;
            $payment->account->save();
        }
        $membership_invoice = $membership->invoice->id;
        $membership->delete();
        Invoice::find($membership_invoice)->delete();

        $this->deleted();
        return back();
    }

    public function massDestroy(MassDestroyMembershipRequest $request)
    {
        Membership::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function freezeRequests($id)
    {
        $membership = Membership::withCount(['freezeRequests', 'service_pricelist', 'member'])->findOrFail($id);
        $diff = 0;
        foreach ($membership->freezeRequests()->whereStatus('confirmed')->get() as $freezeRequest) {

            if ($freezeRequest->end_date <= date('Y-m-d')) {
                $diff += $freezeRequest->freeze;
            } else {
                $now = Carbon::now();
                $start = Carbon::parse($freezeRequest->start_date);
                $diff += $now->diffInDays($start);
            }
        }

        return view('admin.memberships.freezeRequests', compact('membership', 'diff'));
    }

    public function renew($id)
    {
        $membership = Membership::findOrFail($id);

        $selected_branch = isset(Auth()->user()->employee) ? Auth()->user()->employee->branch : Null;

        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        
        $sales_bies = User::whereHas('roles', function ($q) {
                        $q->where('title', 'Sales');
                    })->whereHas('employee', function ($i) use ($selected_branch) {
                        $i->whereStatus('active')->when($selected_branch, function ($q) use ($selected_branch) {
                            $q->whereBranchId($selected_branch->id);
                        });
                    })->pluck('name', 'id');


        $trainers = User::whereHas('roles', function ($q) {
                        $q->where('title', 'Trainer');
                    })->whereHas('employee', function ($i) use ($selected_branch) {
                        $i->whereStatus('active')->when($selected_branch, function ($q) use ($selected_branch) {
                            $q->whereBranchId($selected_branch->id);
                        });
                    })->pluck('name', 'id');

        $pricelists = Pricelist::whereStatus('active')->with(['service'])->latest()->get();

        $sales_bies = User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->whereHas('employee', function ($i) {
            $i->whereStatus('active');
        })->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');


        $last_invoice = Invoice::latest()->first()->id ?? 0;

        $accounts = Account::pluck('name', 'id');

        $statuses = Status::pluck('name', 'id');

        $setting = Setting::first();

        $sports = Sport::pluck('name', 'id');

        $main_schedules = ScheduleMain::with(['session', 'trainer'])
            ->when($selected_branch, function ($q) use ($selected_branch) {
                $q->whereBranchId($selected_branch->id);
            })
            ->whereStatus('active')
            ->whereHas('schedule_main_group', fn($q) => $q->whereStatus('active'))
            ->latest()
            ->get();

        return view('admin.memberships.renew', compact('membership', 'trainers', 'pricelists', 'sales_bies', 'last_invoice', 'accounts', 'statuses', 'setting', 'sports', 'main_schedules', 'selected_branch', 'branches'));
    }

    public function storeRenew(Request $request)
    {
        $selected_branch_id = Auth()->user()->employee->branch ? Auth()->user()->employee->branch : (int)$request['branch_id'];
//        dd($selected_branch_id);
        try {
            DB::beginTransaction();
            $service_type_id = Pricelist::find($request->service_pricelist_id)->service->service_type_id;
            $current_memberships = Membership::where('member_id', $request->member_id)
                ->whereHas('service_pricelist.service.service_type', fn($q) => $q->where('id', $service_type_id))
                ->whereStatus('expired')->get();

            foreach ($current_memberships as $membershit) {
                $membershit->update(['is_changed' => 1]);
            }

            $membership = Membership::create([
                'start_date'            => $request->start_date,
                'end_date'              => $request->end_date,
                'member_id'             => $request->member_id,
                'trainer_id'            => $request->trainer_id,
                'service_pricelist_id'  => $request->service_pricelist_id,
                'sport_id'              => $request->sport_id ?? null,
                'notes'                 => $request->notes,
                'sales_by_id'           => $request['sales_by_id'],
                'created_at'            => $request['created_at'] . date('H:i:s'),
                'membership_status'     => 'renew',
                'is_changed'            => 0
            ]);

            $trackMembership = TrackMembership::create([
                'membership_id'         => $membership->id,
                'status'                => 'renew'
            ]);

            $invoice = Invoice::create([
                'discount'          => $request['discount_amount'],
                'discount_notes'    => $request['discount_notes'],
                'service_fee'       => $request['membership_fee'],
                'net_amount'        => $request->membership_fee - $request->discount_amount,
                'membership_id'     => $membership->id,
                'branch_id'         => $selected_branch_id,
                'sales_by_id'       => $request['sales_by_id'],
                'status'            => ($request->membership_fee - $request->discount_amount) == $request->received_amount ? 'fullpayment' : 'partial',
                'created_by_id'     => Auth()->user()->id,
                'created_at'        => $request['created_at'] . date('H:i:s')
            ]);


            // Venom System
            if ($request['main_schedule_id'] != NULL) {
                foreach ($request['main_schedule_id'] as $key => $main_request) {
                    $main_schedule = ScheduleMain::with(['schedules'])->find($main_request);

                    foreach ($membership->member->memberships as $key => $membership) {
                        if ($membership->status == 'expired') {
                            $membership_schedules = $membership->membership_schedules;
                            foreach ($membership_schedules as $membership_schedule) {
                                $membership_schedule->update([
                                    'is_active' => 'inactive'
                                ]);
                            }
                        }
                    }

                    MembershipSchedule::create([
                        'membership_id' => $membership->id,
                        'schedule_main_id' => $main_schedule->id,
                        'schedule_id' => $main_schedule->schedules->last()->id
                    ]);
                }
            }

            foreach ($request['account_amount'] as $key => $account_amount) {
                if ($account_amount > 0) {
                    $payment = Payment::create([
                        'account_id' => $request->account_ids[$key],
                        'amount' => $account_amount,
                        'invoice_id' => $invoice->id,
                        'sales_by_id' => $request['sales_by_id'],
                        'created_by_id' => Auth()->user()->id,
                        'created_at' => $request['created_at'] . date('H:i:s')
                    ]);

                    $payment->account->balance = $payment->account->balance + $payment->amount;
                    $payment->account->save();

                    $transaction = Transaction::create([
                        'transactionable_type' => 'App\\Models\\Payment',
                        'transactionable_id' => $payment->id,
                        'amount' => $account_amount,
                        'account_id' => $request->account_ids[$key],
                        'created_by' => auth()->user()->id,
                        'created_at' => $request['created_at'] . date('H:i:s')
                    ]);
                }
            }

            if (($invoice->net_amount - $invoice->payments->sum('amount')) > 0) {
                $invoice->status = 'partial';
            } else {
                $invoice->status = 'fullpayment';
            }

            $invoice->save();

            if ($invoice->status == 'partial') {
                // Due Payment reminder
                Reminder::create([
                    'type' => 'due_payment',
                    'membership_id' => $membership->id,
                    'lead_id' => $membership->member_id,
                    'due_date' => $request->due_date != NULL ? $request->due_date : date('Y-m-d', strtotime('+3 Days')),
                    'user_id' => $request['sales_by_id'],
                ]);

                $membership->member->update([
                    'status_id' => Status::firstOrCreate(
                        ['name' => 'Debt Member'],
                        ['color' => 'warning', 'default_next_followup_days' => 1, 'need_followup' => 'yes']
                    )->id
                ]);
            } else {
                $membership->member->update(['status_id' => $request->member_status_id]);
            }

            // Upgrade Reminder
            // Reminder::create([
            //     'type'              => 'upgrade',
            //     'membership_id'     => $membership->id,
            //     'lead_id'           => $membership->member_id,
            //     'due_date'          => date('Y-m-d', strtotime($membership->start_date.'+'.$membership->service_pricelist->upgrade_date.'Days')),
            //     'user_id'           => $request['sales_by_id'],
            // ]);

            // Follow up Reminder
            Reminder::create([
                'type' => 'follow_up',
                'membership_id' => $membership->id,
                'lead_id' => $membership->member_id,
                'due_date' => date('Y-m-d', strtotime($membership->start_date . '+' . $membership->service_pricelist->followup_date . 'Days')),
                'user_id' => $request['sales_by_id'],
            ]);


            // if($request['statuses'] != NULL && $request['due_date'] != NULL) {
            //     Reminder::create([
            //         'lead_id'   => $membership->member->id,
            //         'due_date'  => $request->due_date,
            //         'user_id'   => $request['sales_by_id'],
            //     ]);

            //     Lead::find($membership->member_id)->update(['status_id' => $request->status_id]);
            // }

            DB::commit();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
        }

        $this->created();

        return redirect()->route('admin.invoices.show', $invoice->id);
    }


    public function upgrade($id)
    {
        $membership = Membership::with('member')->findOrFail($id);

        $invoice = Invoice::withSum('payments', 'amount')->whereMembershipId($membership->id)->firstOrFail();

        $pricelists = Pricelist::whereStatus('active')->where('amount', '>', $membership->service_pricelist->amount)->get();

        $selected_branch = isset(Auth()->user()->employee) ? Auth()->user()->employee->branch : Null;

        $sales_bies = User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->whereHas('employee', function ($i) use ($selected_branch) {
            $i->whereStatus('active')->when($selected_branch, function ($q) use ($selected_branch) {
                $q->whereBranchId($selected_branch->id);
            });;
        })->pluck('name', 'id');

        $pricelists = Pricelist::whereStatus('active')->with(['service'])->latest()->get();

        $trainers = User::whereHas('roles', function ($q) {
            $q->where('title', 'Trainer');
        })->whereHas('employee', function ($i) use ($selected_branch) {
            $i->whereStatus('active')->when($selected_branch, function ($q) use ($selected_branch) {
                $q->whereBranchId($selected_branch->id);
            });
        })->pluck('name', 'id');

        // $accounts = Account::pluck('name','id');
        $accounts = Account::when($selected_branch, function ($q) use ($selected_branch) {
            $q->whereBranchId($selected_branch->id);
        })->pluck('name', 'id');

        $memberStatuses = MemberStatus::pluck('name', 'id');

        $setting = Setting::first();

        $sports = Sport::pluck('name', 'id');

        $main_schedules = ScheduleMain::with(['session', 'trainer'])
            ->when($selected_branch, function ($q) use ($selected_branch) {
                $q->whereBranchId($selected_branch->id);
            })
            ->whereStatus('active')
            ->whereHas('schedule_main_group', fn($q) => $q->whereStatus('active'))
            ->latest()
            ->get();

        return view('admin.memberships.upgrade', compact('membership', 'invoice', 'pricelists', 'trainers', 'sales_bies', 'accounts', 'memberStatuses', 'setting', 'sports', 'main_schedules'));
    }

    public function storeUpgrade(Request $request, $id)
    {
        $membership = Membership::with(['invoice', 'member', 'member.memberships'])->findOrFail($id);
        $member_id = $membership->member_id;
        $sales_by_id = $membership->member->sales_by_id;
        try {
            DB::beginTransaction();

            $membership->update([
                'service_pricelist_id' => $request['service_pricelist_id'],
                'trainer_id' => $request['trainer_id'],
                'sales_by_id' => $request['sales_by_id'],
                // 'created_at'            => $request['created_at'],
                'start_date' => $request['start_date'],
                'end_date' => $request['end_date'],
                'notes' => $request['subscription_notes'],
                'sport_id' => $request->sport_id ?? null
            ]);

            // Reminders
            $this->welcome_call($membership);

            // Renew Reminders
            $this->renew_call($membership);

            $trackMembership = TrackMembership::create([
                'membership_id' => $membership->id,
                'status' => 'upgrade'
            ]);

            $membership->invoice->update([
                'discount' => $request->discount_amount,
                'discount_notes' => $request->discount_notes,
                'service_fee' => $request->membership_fee,
                'net_amount' => $request->membership_fee - $request->discount_amount,
                'membership_id' => $membership->id,
                'sales_by_id' => $request['sales_by_id'],
                'status' => ($request->membership_fee - $request->discount_amount) == $request->received_amount ? 'fullpayment' : 'partial',
                'created_by_id' => Auth()->user()->id,
                // 'created_at'        => $request['created_at'].date('H:i:s'),
                'is_reviewed' => 0
            ]);

            $invoice = $membership->invoice;


            // Venom System
            if ($request['main_schedule_id'] != NULL) {
                foreach ($request['main_schedule_id'] as $key => $main_request) {
                    $main_schedule = ScheduleMain::with(['schedules'])->find($main_request);

                    foreach ($membership->member->memberships as $key => $membership) {
                        if ($membership->status == 'expired') {
                            $membership_schedules = $membership->membership_schedules;
                            foreach ($membership_schedules as $membership_schedule) {
                                $membership_schedule->update([
                                    'is_active' => 'inactive'
                                ]);
                            }
                        }
                    }

                    MembershipSchedule::create([
                        'membership_id' => $membership->id,
                        'schedule_main_id' => $main_schedule->id,
                        'schedule_id' => $main_schedule->schedules->last()->id
                    ]);
                }
            }

            foreach ($request['account_amount'] as $key => $account_amount) {
                if ($account_amount > 0) {
                    $payment = Payment::create([
                        'account_id' => $request->account_ids[$key],
                        'amount' => $account_amount,
                        'invoice_id' => $invoice->id,
                        'sales_by_id' => $request['sales_by_id'],
                        'created_by_id' => Auth()->user()->id,
                        'created_at' => $request['created_at'] . date('H:i:s')
                    ]);

                    $payment->account->balance = $payment->account->balance + $payment->amount;
                    $payment->account->save();

                    $transaction = Transaction::create([
                        'transactionable_type' => 'App\\Models\\Payment',
                        'transactionable_id' => $payment->id,
                        'amount' => $account_amount,
                        'account_id' => $request->account_ids[$key],
                        'created_by' => auth()->user()->id,
                        'created_at' => $request['created_at'] . date('H:i:s')
                    ]);
                }
            }

            if (!is_null($membership->reminders) && !is_null($membership->reminders()->whereType('upgrade'))) {
                $membership->reminders()->whereType('upgrade')->delete();
            }

            if ($invoice->status == 'partial') {
                // Due payment reminder
                Reminder::create([
                    'type' => 'due_payment',
                    'membership_id' => $membership->id,
                    'lead_id' => $member_id,
                    'due_date' => $request->due_date != NULL ? $request->due_date : date('Y-m-d', strtotime('+3 Days')),
                    'user_id' => $sales_by_id,
                ]);

                $membership->member->update([
                    'status_id' => Status::firstOrCreate(
                        ['name' => 'Debt Member'],
                        ['color' => 'warning', 'default_next_followup_days' => 1, 'need_followup' => 'yes']
                    )->id
                ]);
            } else {
                $membership->member->update(['status_id' => $request->member_status_id]);
            }

            DB::commit();
        } catch (\Exception $e) {
            dd($e);
            $this->something_wrong();
            return back();
        }

        $this->created();
        return redirect()->route('admin.invoices.show', $invoice->id);
    }

    public function downgrade($id)
    {
        $membership = Membership::with('member')->findOrFail($id);

        $invoice = Invoice::withSum('payments', 'amount')->whereMembershipId($membership->id)->firstOrFail();

        $pricelists = Pricelist::whereStatus('active')->where('amount', '<', $membership->service_pricelist->amount)->get();

        $selected_branch = isset(Auth()->user()->employee) ? Auth()->user()->employee->branch : Null;

        $sales_bies = User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->whereHas('employee', function ($i) use ($selected_branch) {
            $i->whereStatus('active')->when($selected_branch, function ($q) use ($selected_branch) {
                $q->whereBranchId($selected_branch->id);
            });
        })->pluck('name', 'id');

        $pricelists = Pricelist::whereStatus('active')->with(['service'])->latest()->get();

        $trainers = User::whereHas('roles', function ($q) {
            $q->where('title', 'Trainer');
        })->whereHas('employee', function ($i) use ($selected_branch) {
            $i->whereStatus('active')->when($selected_branch, function ($q) use ($selected_branch) {
                $q->whereBranchId($selected_branch->id);
            });
        })->pluck('name', 'id');

        // $accounts = Account::pluck('name','id');
        $accounts = Account::when($selected_branch, function ($q) use ($selected_branch) {
            $q->whereBranchId($selected_branch->id);
        })->pluck('name', 'id');


        $memberStatuses = MemberStatus::pluck('name', 'id');

        $setting = Setting::first();

        $sports = Sport::pluck('name', 'id');

        $main_schedules = ScheduleMain::with(['session', 'trainer'])
            ->whereStatus('active')
            ->when($selected_branch, function ($q) use ($selected_branch) {
                $q->whereBranchId($selected_branch->id);
            })
            ->whereHas('schedule_main_group', fn($q) => $q->whereStatus('active'))
            ->latest()
            ->get();

        return view('admin.memberships.downgrade', compact('membership', 'invoice', 'pricelists', 'trainers', 'sales_bies', 'accounts', 'memberStatuses', 'setting', 'sports', 'main_schedules'));
    }

    public function storeDowngrade(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $membership = Membership::with(['invoice', 'member', 'member.memberships'])->findOrFail($id);
            $sales_by_id = $membership->member->sales_by_id;

            foreach ($request['account_amount'] as $key => $account_amount) {
                if ($account_amount < 0) {
                    $payment = Payment::create([
                        'account_id' => $request->account_ids[$key],
                        'amount' => $account_amount,
                        'invoice_id' => $membership->invoice->id,
                        'sales_by_id' => $request['sales_by_id'],
                        'created_by_id' => Auth()->user()->id,
                        'created_at' => $request['created_at'] . date('H:i:s')
                    ]);

                    $payment->account->balance = $payment->account->balance + $payment->amount;
                    $payment->account->save();

                    $transaction = Transaction::create([
                        'transactionable_type' => 'App\\Models\\Payment',
                        'transactionable_id' => $payment->id,
                        'amount' => $account_amount,
                        'account_id' => $request->account_ids[$key],
                        'created_by' => auth()->user()->id,
                        'created_at' => $request['created_at'] . date('H:i:s')
                    ]);
                }
            }

            $membership->invoice->update([
                'net_amount' => $request['net_amount'],
                'service_fee' => $request['membership_fee'],
                'status' => 'fullpayment',
                'is_reviewed' => 0,
                'discount' => $request['discount'],
                'discount_notes' => $request['discount_notes']

            ]);


            // Venom System
            if ($request['main_schedule_id'] != NULL) {
                foreach ($request['main_schedule_id'] as $key => $main_request) {
                    $main_schedule = ScheduleMain::with(['schedules'])->find($main_request);

                    foreach ($membership->member->memberships as $key => $membership) {
                        if ($membership->status == 'expired') {
                            $membership_schedules = $membership->membership_schedules;
                            foreach ($membership_schedules as $membership_schedule) {
                                $membership_schedule->update([
                                    'is_active' => 'inactive'
                                ]);
                            }
                        }
                    }

                    MembershipSchedule::create([
                        'membership_id' => $membership->id,
                        'schedule_main_id' => $main_schedule->id,
                        'schedule_id' => $main_schedule->schedules->last()->id
                    ]);
                }
            }

            $membership->update([
                'service_pricelist_id' => $request['service_pricelist_id'],
                'trainer_id' => $request['trainer_id'],
                'sales_by_id' => $sales_by_id,
                'start_date' => $request['start_date'],
                'end_date' => $request['end_date'],
                'sport_id' => $request['sport_id'] ?? null
            ]);

            // Reminders
            $this->welcome_call($membership);

            // Renew Reminders
            $this->renew_call($membership);

            $trackMembership = TrackMembership::create([
                'membership_id' => $membership->id,
                'status' => 'downgrade'
            ]);

            DB::commit();
        } catch (\Exception $e) {
            dd($e);
            $this->something_wrong();
            return back();
        }

        $this->created();
        return redirect()->route('admin.invoices.show', $membership->invoice->id);
    }

    public function getMember(Request $request)
    {
        $member = Lead::where('type', 'member')
            ->whereBranchId($request['member_branch_id'])
            ->whereMemberCode($request['member_code'])
            ->firstOrFail();

        $membership = Membership::with(['service_pricelist', 'member', 'member.branch'])->whereMemberId($member->id)
            ->whereHas('service_pricelist', function ($q) {
                $q->whereHas('service', function ($x) {
                    $x->whereHas('service_type', function ($p) {
                        $p->whereMainService(true);
                    });
                });
            })
            ->whereIn('status', ['expiring', 'current'])
            ->first();

        if (!$membership) {
            $membership = Membership::with(['service_pricelist', 'member', 'member.branch'])->where('member_id', $member->id)->first();
        }

        $freeze = null;
        if (!is_null($membership)) {
            $freeze = FreezeRequest::with(['membership' => fn($q) => $q->with('service_pricelist')])
                ->whereMembershipId($membership->id)
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('end_date', '>', date('Y-m-d'))
                ->whereStatus('confirmed')
                ->first();
        }

        return response()->json([
            'member' => $member,
            'membership' => $membership,
            'freeze' => $freeze,
        ]);
    }

    public function expiredMemberships(Request $request)
    {
        if ($request['sales_by_id']) {
            $members = Lead::where('type', '=', 'member')->where('sales_by_id', $request['sales_by_id'])->get();
        } else {
            $members = Lead::where('type', '=', 'member')->get();
        }

        $membership_array = [];
        foreach ($members as $member) {
            if ($request->end_date) {
                $last_main_subscription_membership = Membership::where('member_id', $member->id)
                    ->whereHas('service_pricelist.service.service_type', fn($q) => $q->where('main_service', 1))
                    // ->where('end_date','>=',$request->end_date['from'])
                    // ->where('end_date','<=',$request->end_date['to'])
                    ->orderBy('end_date', 'desc')->first();

                if ($last_main_subscription_membership) {
                    if ($last_main_subscription_membership->status == 'expired') {
                        array_push($membership_array, $last_main_subscription_membership->id);
                    }
                }
            } else {
                $last_main_subscription_membership = Membership::where('member_id', $member)
                    ->whereHas('service_pricelist.service.service_type', fn($q) => $q->where('main_service', 1))
                    ->orderBy('end_date', 'desc')->first();
                if ($last_main_subscription_membership) {
                    if ($last_main_subscription_membership->status == 'expired') {
                        array_push($membership_array, $last_main_subscription_membership->id);
                    }
                }
            }
        }
        $memberships = Membership::whereIn('id', $membership_array);

        if (isset($request['start_date']) && $request['start_date']['from']) {
            $memberships = $memberships
                ->where('start_date', '>=', $request['start_date']['from'])
                ->where('start_date', '<=', $request['start_date']['to'])
                ->paginate(20);
        } elseif (isset($request['end_date']) && $request['end_date']['from']) {
            $memberships = $memberships
                ->where('end_date', '>=', $request['end_date']['from'])
                ->where('end_date', '<=', $request['end_date']['to'])
                ->paginate(20);
        } else {
            $memberships = $memberships->paginate(20);
        }

        $sales = User::with('roles')->whereHas('roles', function ($q) {
            $q = $q->whereTitle('sales');
        })->pluck('name', 'id');

        $setting = Setting::firstOrFail();

        $memberStatuses = MemberStatus::pluck('name', 'id');
        return view('admin.memberships.expired', compact('memberships', 'memberStatuses', 'sales', 'setting'));
    }

    public function expiredMembershipsExtra(Request $request)
    {
        if ($request['sales_by_id']) {
            $members = Lead::where('type', '=', 'member')->where('sales_by_id', $request['sales_by_id'])->get();
        } else {
            $members = Lead::where('type', '=', 'member')->get();
        }
        $membership_array = [];
        foreach ($members as $member) {

            $last_main_subscription_membership = Membership::where('member_id', $member->id)
                ->where('trainer_id', $request['trainer_id'])
                ->whereHas('service_pricelist.service.service_type', fn($q) => $q->where('main_service', 0))
                ->orderBy('id', 'desc')->first();
            if ($last_main_subscription_membership) {
                if ($last_main_subscription_membership->status == 'expired') {
                    array_push($membership_array, $last_main_subscription_membership->id);
                }
            }
        }

        $memberships = Membership::whereHas('invoice')->whereIn('id', $membership_array);

        if (isset($request['start_date']) && $request['start_date']['from']) {
            $memberships = $memberships
                ->where('start_date', '>=', $request['start_date']['from'])
                ->where('start_date', '<=', $request['start_date']['to'])
                ->paginate(20);
        } elseif (isset($request['end_date']) && $request['start_date']['from']) {
            $memberships = $memberships
                ->where('end_date', '>=', $request['end_date']['from'])
                ->where('end_date', '<=', $request['end_date']['to'])
                ->paginate(20);
        } else {
            $memberships = $memberships->paginate(20);
        }


        $sales = User::with('roles')->whereHas('roles', function ($q) {
            $q = $q->whereTitle('sales');
        })->pluck('name', 'id');

        $trainers = User::with('roles')->whereHas('roles', function ($q) {
            $q = $q->whereTitle('trainer');
        })->pluck('name', 'id');

        $setting = Setting::firstOrFail();

        $memberStatuses = MemberStatus::pluck('name', 'id');
        return view('admin.memberships.expiredExtra', compact('memberships', 'memberStatuses', 'sales', 'setting', 'trainers'));
    }

    public function expiring_expired(Request $request)
    {
        $sales          = User::whereRelation('roles','title','Sales')->orderBy('name')->pluck('name', 'id');

        $branches      = Branch::orderBy('name')->pluck('name', 'id');

        $service_types = ServiceType::orderBy('name')->pluck('name', 'id');

        $setting = Setting::firstOrFail();

        $expiring_memberships = Membership::index($request->all())
            ->withCount('attendances')
            ->withCount('trainer_attendances')
            ->with(['service_pricelist.service.service_type','member.branch','assigned_coach','sales_by'])
            ->whereIn('status',['expiring','expired'])
            ->latest();
        if(Auth()->user()->employee->branch){
            $expiring_memberships->whereHas('member',fn($x)=>$x->whereHas('branch',fn($i)=>$i->where('id',Auth()->user()->employee->branch->id)));
        };
           $expiring_memberships=$expiring_memberships->get();

        return view('admin.memberships.expiring_expired', compact('expiring_memberships', 'sales', 'setting','branches','service_types'));
    }

    public function reminderExpiredMemberships(Request $request)
    {
        foreach ($request['member_ids'] as $key => $member_id) {

            $member = Lead::find($member_id);

            if ($member->leadReminder) 
            {
                $member->leadReminder()->delete();
            }

            $newReminder = Reminder::create([
                'lead_id'           => $member_id,
                'due_date'          => $request->due_date,
                'user_id'           => $member->sales_by_id,
            ]);

            $reminderHistory = LeadRemindersHistory::create([
                'lead_id'           => $member_id,
                'due_date'          => $request->due_date,
                'action_date'       => date('Y-m-d'),
                'member_status_id'  => $request->member_status_id,
                'notes'             => $request->notes,
                'user_id'           => $member->sales_by_id,
            ]);
        }

        $this->created();
        return back();
    }

    public function transferMembership($id)
    {
        $membership = Membership::findOrFail($id);
        $last_member_code = Lead::whereType('member')->whereDeletedAt(Null)->orderBy('member_code', 'desc')->first()->member_code ?? 1;

        $members = Lead::whereType('member')->pluck('name', 'id');
        $accounts = Account::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $sources = Source::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $addresses = Address::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $sales_bies = User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->whereHas('employee', function ($i) {
            $i->whereStatus('active');
        })->pluck('name', 'id');

        return view('admin.memberships.transferMembership', compact('membership', 'members', 'accounts', 'last_member_code', 'sources', 'addresses', 'sales_bies'));
    }

    public function fix()
    {
        //
    }

    public function storeTransferMembership(Request $request, $id)
    {
        $request->validate([
            'email' => 'nullable|unique:users,email',
            'national' => 'nullable|min:14|max:14|unique:leads,national',
            'phone' => 'required_if:member_type,new_member|min:11|max:11|unique:leads,phone',
            'lead' => 'required',
            'member_code' => 'required_if:member_type,new_member|unique:leads,member_code',
            'source_id' => 'required_if:member_type,new_member',
            'address_id' => 'required_if:member_type,new_member',
            'dob' => 'required_if:member_type,new_member',
            'gender' => 'required_if:member_type,new_member',
            'sales_by_id' => 'required_if:member_type,new_member',
            'amount' => 'bail|required',
            'account' => 'bail|required|exists:accounts,id'
        ]);


        $membership = Membership::findOrFail($id);
        if ($request->member_type == 'new_member') {
            $user = User::create([
                'name' => $request->lead,
                'email' => $request->email,
                'password' => bcrypt($request->phone),
                'email' => isset($request->email) && (!is_null($request->email)) ? $request->email : str_replace(' ', '_', $request->lead) . $request->member_code . '@gmail.com',
            ]);

            $member = Lead::create([
                'name' => $request->lead,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'national' => $request->national,
                'member_code' => $request->member_code,
                'dob' => $request->dob,
                'source_id' => $request->source_id,
                'address_id' => $request->address_id,
                'sales_by_id' => $request->sales_by_id,
                'card_number' => $request->card_number,
                'user_id' => $user->id,
                'referral_member' => $request->referral_member,
                'type' => 'member',
                'created_by_id' => auth()->user()->id
            ]);
            $membership->update([
                'member_id' => $member->id
            ]);
        } else {
            $membership->update([
                'member_id' => $request->member_type
            ]);
        }

        $external_payment = ExternalPayment::create([
            'title' => $request->title . ' OF ( ' . Membership::findOrFail($id)->service_pricelist->name . ' ) TO ' . $request->lead . ' NATIONAL : ' . $request->national,
            'amount' => $request->amount,
            'account_id' => $request->account,
            'created_by_id' => auth()->id(),
            'lead_id' => $member->id ?? $request->member_type,
            'created_by_id' => auth()->user()->id
        ]);

        $this->transfered();

        return redirect()->route('admin.memberships.index');
    }

    public function export_expiring_expired(Request $request)
    {
        return Excel::download(new ExpiringExpiredExport($request->all()), 'expiring-expired-memberships.xlsx');
    }

    public function exportMainExpired(Request $request)
    {
        return Excel::download(new MainExpiredMembershipsExport($request->all()), 'main-expired_memberships.xlsx');
    }

    public function exportPtExpired(Request $request)
    {
        return Excel::download(new PtExpiredMembershipsExport($request->all()), 'pt-expired-memberships.xlsx');
    }

    public function addMembershipServiceOption($membership_id, $service_option_pricelist_id)
    {
        $membership_service_option = MembershipServiceOptions::create([
            'service_option_pricelist_id' => $service_option_pricelist_id,
            'membership_id' => $membership_id,
            'count' => 1
        ]);

        $counter = MembershipServiceOptions::where('service_option_pricelist_id', $service_option_pricelist_id)->where('membership_id', $membership_id)->count();

        return response()->json(['new_counter' => $counter]);
    }

    public function exportMemberships(Request $request)
    {
        return Excel::download(new MembershipsExport($request), 'Memberships.xlsx');
    }

    public function assign_coach_to_non_pt_mempers(Membership $membership)
    {
        return response()->json($membership);
    }

    public function assign(Request $request, Membership $member)
    {
        if ($request->input('selectedMembers')) {
            $members = Str::of($request->selectedMembers)->explode(',');
            foreach ($members as $member) {
                $foundMember = Membership::findOrFail($member);

                $new_reminder = Reminder::create([
                    'type' => 'pt_session',
                    'user_id' => $request['assigned_coach_id'],
                    'membership_id' => $foundMember->id,
                    'due_date' => date('Y-m-d'),
                    'lead_id' => $foundMember->member_id
                ]);

                $foundMember->update([
                    'assigned_coach_id' => $request['assigned_coach_id']
                ]);
            }
        }
        return back();
    }

    public function assignTrainer(Request $request,Membership $membership)
    {
        $membership->load(['reminders' => fn($q) => $q->whereType('pt_session')])->loadCount('reminders');

        if ($membership->reminders_count > 0) 
        {
            foreach ($membership->reminders as $key => $reminder) 
            {
                if ($request['type'] == 'pt' && $request['to_trainer_id'] == NULL || $request['type'] == 'non_pt' && $request['to_trainer_id_non_pt'] == NULL) 
                {
                    $reminder->delete();
                }else{
                    $reminder->update([
                        'user_id'       => $request['type'] == 'pt' ? $request['to_trainer_id'] : $request['to_trainer_id_non_pt']
                    ]);
                }
            }
        }else{
            $new_reminder = Reminder::create([
                'type'          => 'pt_session',
                'user_id'       => $request['type'] == 'pt' ? $request['to_trainer_id'] : $request['to_trainer_id_non_pt'],
                'membership_id' => $membership->id,
                'due_date'      => date('Y-m-d'),
                'lead_id'       => $membership->member_id
            ]);
        }

        $membership->update([
            'trainer_id'            => $request['type'] == 'pt' ? $request['to_trainer_id'] : NULL,
            'assigned_coach_id'     => $request['type'] == 'pt' ? $request['to_trainer_id'] : $request['to_trainer_id_non_pt'],
            'assign_date'           => date('Y-m-d H:i:s')
        ]);

        return back();
    }

    public function assigned_memberships(Request $request)
    {
        if (isset($request->q)){
           $results = Lead::whereType('member')->where('name', 'LIKE', "{$request->q}%")->orWhere('member_code','LIKE',"{$request->q}%")->limit(10)->get();
            return response()->json($results->map(function($item) {
                return ['id' => $item->id, 'name' => $item->name,'member_code'=>$item->branch->member_prefix.$item->member_code];
            }));
        }

        abort_if(Gate::denies('membership_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);

        $counter = Membership::index($data)->get()->count();

        $employee = Auth()->user()->employee;

        $user = Auth()->user();

        if ($request->ajax()) {
            if ($employee && $employee->branch_id != NULL) {
                if ($user->roles[0]->title == 'Fitness Manager') 
                {
                    $query = Membership::index($data)
                            ->whereHas('member', function ($q) use ($employee) {
                                $q->whereBranchId($employee->branch_id);
                            })
                            ->with(['member', 'trainer', 'service_pricelist', 'sales_by', 'service_pricelist.service', 'service_pricelist.service.service_type', 'member.branch', 'sport','assigned_coach'])
                            ->whereHas('assigned_coach')
                            ->whereStatus('current')
                            ->withCount('attendances')
                            ->withCount('trainer_attendances')
                            ->latest();
                            
                }elseif($user->roles[0]->title == 'Trainer'){
                    $query = Membership::index($data)
                            ->whereHas('member', function ($q) use ($employee) {
                                $q->whereBranchId($employee->branch_id);
                            })
                            ->with(['member', 'trainer', 'service_pricelist', 'sales_by', 'service_pricelist.service', 'service_pricelist.service.service_type', 'member.branch', 'sport','assigned_coach'])
                            ->whereAssignedCoachId($user->id)
                            ->whereStatus('current')
                            ->withCount('attendances')
                            ->withCount('trainer_attendances')
                            ->latest();
                }else {
                    $query = Membership::index($data)
                        ->whereHas('member', function ($q) use ($employee) {
                            $q->whereBranchId($employee->branch_id);
                        })
                        ->with(['member', 'trainer', 'service_pricelist', 'sales_by', 'service_pricelist.service', 'service_pricelist.service.service_type', 'member.branch', 'sport','assigned_coach'])
                        ->withCount('attendances')
                        ->withCount('trainer_attendances')
                        ->latest();
                    // ->select(sprintf('%s.*', (new Membership())->table));
                }
            } else {
                $query = Membership::index($data)
                    ->whereHas('member')
                    ->with(['member', 'trainer', 'service_pricelist', 'sales_by', 'service_pricelist.service', 'service_pricelist.service.service_type', 'member.branch', 'sport','assigned_coach'])
                    ->withCount('attendances')
                    ->withCount('trainer_attendances')
                    ->latest();
                // ->select(sprintf('%s.*', (new Membership())->table));
            }

            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $counter = $query->get()->count();

            $table->editColumn('actions', function ($row) {
                $viewGate = 'membership_show';
                $editGate = 'membership_edit';
                $deleteGate = 'membership_dele';
                $crudRoutePart = 'memberships';
                // $last_attendance = MembershipAttendance::whereMembershipId($row->id)->whereSignOut(NULL)->latest()->first();

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row',
                // 'last_attendance'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });

            $table->addColumn('member_code', function ($row) {
                return $row->member ? $row->member->branch->member_prefix . $row->member->member_code : '';
            });

            $table->addColumn('member_gender', function ($row) {
                return $row->member ? Str::ucfirst($row->member->gender) : '';
            });

            $table->addColumn('member_name', function ($row) {
                return $row->member->name ? $row->member->branch->member_prefix . $row->member->member_code . '<br>' . '<a href="' . route('admin.members.show', $row->member->id) . '" target="_blank">' . $row->member->name . '</a>'
                    . '<br/>' . '<b>' . $row->member->phone . '<b>'
                    . '<br/>' . '<b>' . Lead::GENDER_SELECT[$row->member->gender] . '</b>' : '';
                // return $row->member ? $row->member->name : '';
            });

            $table->addColumn('member_phone', function ($row) {
                return $row->member ? '<a href="' . route('admin.members.show', $row->member_id) . '" target="_blank">' . $row->member->phone . '</a>' : '';
                // return $row->member ? $row->member->name : '';
            });

            // $table->addColumn('remaining_sessions', function ($row) {
            //     return $row->member && $row->service_pricelist && $row->service_pricelist->service && $row->service_pricelist->service->service_type &&  ($row->service_pricelist->service->service_type->session_type == 'sessions' || $row->service_pricelist->service->service_type->session_type == 'group_sessions') ? $row->attendances()->count() . ' \\ ' . $row->service_pricelist->session_count : ' - ';
            // });
            $table->addColumn('remaining_sessions', function ($row) {
                return $row->member && $row->service_pricelist && $row->service_pricelist->service && $row->service_pricelist->service->service_type && $row->service_pricelist->service->service_type->session_type == 'non_sessions' ? ($row->attendances_count . ' \\ ' . $row->service_pricelist->session_count) : ($row->trainer_attendances_count . ' \\ ' . $row->service_pricelist->session_count);
            });

            // $table->addColumn('trainer_name', function ($row) {
            //     return $row->trainer ? $row->trainer->name : '';
            // });
            $table->addColumn('assigned_coach_name', function ($row) {
                return $row->assigned_coach ? $row->assigned_coach->name : '';
            });

            $table->addColumn('status', function ($row) {
                return $row->status ? "<span class='badge badge-" . Membership::MEMBERSHIP_STATUS_COLOR[$row->membership_status] . " p-2'>" .
                    $row->membership_status . "</span>" . '<br>' .
                    "<span class='font-weight-bold p-2 badge badge-" . Membership::STATUS[$row->status] . "'>"
                    . ucfirst(Membership::SELECT_STATUS[$row->status]) . "</span>" : '';
            });

            $table->addColumn('service_pricelist_name', function ($row) {
                return $row->service_pricelist ? $row->service_pricelist->name . '<br>' . '<span class="badge badge-info">' . $row->service_pricelist->service->service_type->name . '</span>' : '';
            });

            $table->addColumn('sales_by_name', function ($row) {
                return $row->sales_by ? $row->sales_by->name : '';
            });

            $table->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at : '';
            });

            $table->editColumn('sport', function ($row) {
                return $row->sport_id != NULL ? $row->sport->name : '----';
            });

            $table->addColumn('branch_name', function ($row) {
                return $row->member && $row->member->branch ? $row->member->branch->name : '-';
            });

            $table->editColumn('last_attendance', function ($row) {
                return $row->last_attendance ? $row->last_attendance : 'No Attendance';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'status', 'member', 'trainer', 'service_pricelist', 'sales_by', 'member_name', 'member_phone', 'member_code', 'service_pricelist_name', 'branch_name','assigned_coach_name']);

            return $table->make(true);
        }


        $locker_statuses = [
            'nottaken' => 'Not taken',
            'taken' => 'Taken',
            'returned' => 'Returned'
        ];

        $lockers = Locker::pluck('code', 'id');

//        $members = Lead::whereType('member')->whereHas('memberships')->pluck('name', 'id');

        $sales = User::whereHas('roles', function ($q) {
            $q = $q->whereTitle('sales');
        })->pluck('name', 'id');

        $services = Pricelist::pluck('name', 'id');

        $service_types = ServiceType::pluck('name', 'id');

        $trainers = User::whereHas('roles', function ($q) {
            $q->where('title', 'Trainer');
        })->whereHas('employee', function ($i) {
            $i->whereStatus('active');
        })->pluck('name', 'id');

        $memberStatuses = MemberStatus::pluck('name', 'id');

        $members = Lead::whereType('member')->limit(20)->pluck('name', 'id');

        $branches = Branch::pluck('name', 'id');

        return view('admin.memberships.assigned_memberships', compact( 'locker_statuses', 'lockers', 'members', 'sales', 'services', 'trainers', 'memberStatuses', 'counter', 'service_types', 'branches'));
    }
}
