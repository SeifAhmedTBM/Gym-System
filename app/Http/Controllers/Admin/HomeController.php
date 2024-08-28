<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\Trainer;
use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Loan;
use App\Models\User;
use App\Models\Branch;
use App\Models\Refund;
use App\Models\Source;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Reminder;
use App\Models\Schedule;
use App\Models\Timeslot;
use App\Models\Pricelist;
use App\Models\SalesTier;
use App\Models\Membership;
use App\Models\Transaction;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use App\Models\FreezeRequest;
use App\Models\MemberReminder;
use App\Models\ExternalPayment;
use App\Models\TrackMembership;
use App\Models\EmployeeSchedule;
use App\Models\LeadRemindersHistory;
use App\Models\TrainerAttendant;
use App\Models\MembershipAttendance;
use Illuminate\Support\Facades\Auth;

class HomeController
{
    public function index(Request $request)
    {


        // $expenses = Expense::all();
        // foreach($expenses as $expense){
        //     $transaction = $expense->transaction;
        //     $transaction->amount = $expense->amount;
        //     $transaction->save();
        // }

        // $refunds = Refund::all();
        // foreach($refunds as $refund){
        //     $transaction = $refund->transaction;
        //     if($transaction){
        //     $transaction->amount = $refund->amount;

        //     }
        //     $transaction->save();
        // }

        // $exs = ExternalPayment::all();
        // foreach($exs as $ex){
        //     $transaction = $ex->transaction;
        //     $transaction->amount = $ex->amount;
        //     $transaction->save();
        // }


        // $payments = Payment::all();
        // foreach($payments as $payment){
        //     $transaction = $payment->transaction;
        //     $transaction->amount = $payment->amount;
        //     $transaction->save();
        // }


        // $loans = Loan::all();
        // foreach($loans as $loan){
        //     $transaction = $loan->transaction;
        //     $transaction->amount = $loan->amount;
        //     $transaction->save();
        // }


        // dd(auth()->user()->roles[0]->title);
        // $users = User::index(request()->all())->whereHas('Roles',function($q){
        //     $q->where('title','!=','Developer');
        // })->with(['roles'])->get();
        // foreach($users as $index=>$user){
        //     $user->order = $index+1;
        //     $user->save();
        // }

        // $pricelists = Pricelist::orderBy('created_at','asc')->get();
        // foreach($pricelists as $index => $pricelist){
        //     $pricelist->order = $index+1;
        //     $pricelist->save();
        // }

        // $services = Service::orderBy('created_at','asc')->get();
        // foreach($services as $index => $service){
        //     $service->order = $index+1;
        //     $service->save();
        // }

        // $employees = Employee::orderBy('created_at','asc')->get();
        // foreach($employees as $index => $employee){
        //     $employee->order = $index+1;
        //     $employee->save();
        // }

        // $trainer_attendances = TrainerAttendant::with([
        //                                 'membership' => fn($q) => $q->withTrashed(),
        //                             ])
        //                             ->get();

        //                             // return $trainer_attendances;

        // foreach ($trainer_attendances as $key => $attend) 
        // {
        //     MembershipAttendance::create([
        //         'sign_in'               => date('H:i:s',strtotime($attend->created_at)),
        //         'sign_out'              => date('H:i:s',strtotime($attend->created_at.' + 2 hours')),
        //         'membership_id'         => $attend->membership_id,
        //         'branch_id'             => $attend->membership->member->branch_id ?? Branch::first()->id,
        //         'created_at'            => $attend->created_at,
        //         'membership_status'     => $attend->membership->status,
        //         'updated_at'            => date('Y-m-d H:i:s'),
        //     ]);
        // }

        // return 1;



        // $attendances = MembershipAttendance::whereSignOut(Null)
        //    ->whereDate('created_at', '<', date('Y-m-d'))
        //    ->get();

        // foreach ($attendances as $key => $attend) {
        //    $attend->update([
        //        'sign_out'       => date('H:i', strtotime($attend->sign_in . '+ 2 hours'))
        //    ]);
        // }

        switch (auth()->user()->roles[0]->title) {
            case 'Super Admin':
                return $this->superAdmin($request);
                break;
            case 'Partner':
                return $this->superAdmin($request);
                break;
            case 'Admin':
                return $this->admin();
                break;
            case 'Developer':
                return $this->admin();
                break;
            case 'Sales':
                return $this->sales();
                break;
            case 'Sales Manager':
                return $this->admin();
                break;
            case 'Trainer':
                return $this->trainer();
                break;
            case 'Receptionist':
                return $this->receptionist();
                break;
            case 'Super Visor':
                return $this->superVisor();
                break;
            case 'Accountant':
                return $this->accountant();
                break;
            case 'Sales manager':
                return $this->sales_manager();
                break;
            case 'Sales Director':
                return $this->sales_director(request());
                break;
            case 'Fitness Manager':
                return $this->fitness_manager(request());
                break;
            default:
                return $this->admin();
                break;
        }
    }

    public function superAdmin(Request $request)
    {
        $date = isset($request['date']) ? $request['date'] : date('Y-m');
        $dateArray = explode('-', $date);
        $currentMonth = $dateArray[1];
        $currentYear = $dateArray[0];
     

        $today = Carbon::now();
        $today2 = Carbon::now();
        
        $startOfLastMonth = $today->subMonth()->startOfMonth();
       
        $endOfLastMonth = $today2->subMonth();
    

        $branches = Branch::with([
            'accounts',
            'transactions' => fn ($q) => $q->whereYear('transactions.created_at', date('Y', strtotime($date)))
                ->whereMonth('transactions.created_at', date('m', strtotime($date)))
        ])->get();

       
        $lastMonthBranchesTransactions = Branch::with(['transactions' => function($query) use ($startOfLastMonth, $endOfLastMonth, $today ,$today2) {
            $query->whereDate('transactions.created_at', '>=', $startOfLastMonth)->whereDate('transactions.created_at', '<=', $endOfLastMonth);
        }])->get();


       $schedules = Schedule::with(['trainer', 'timeslot'])
        // ->whereHas('schedule_main', function ($q) {
        //     $q->whereHas('schedule_main_group', fn ($y) => $y->whereStatus('active'));
        // })
        ->whereYear('date', $currentYear) // Use whereYear for year comparison
        ->whereMonth('date', $currentMonth) // Use whereMonth for month comparison
        ->get()
        ->groupBy('timeslot_id');
       
        $timeslots = Timeslot::orderBy('from', 'asc')->get();

        // $accounts = Account::whereManager(false)
        // ->when($branch_id, fn ($x) => $x->whereBranchId($branch_id))
        // ->with(['transactions' => fn ($q) => $q->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)])
        // ->orderBy('name')
        // ->get();



        return view('home', compact('schedules','timeslots','branches' ,'lastMonthBranchesTransactions' ,'startOfLastMonth' ,'endOfLastMonth'));
    }

    public function admin()
    {
        // $sales = User::whereRelation('roles','title','Sales')->get()->chunk(2);
        // $sales = User::whereRelation('roles','title','Sales')->lazy()->all();
        // $sales = User::whereRelation('roles','title','Sales')->get()->dump();
        // $sales = User::whereRelation('roles','title','Sales')->get()->every(function($value,$key){
        //     return $value;
        // });

        $selected_branch = Auth()->user()->employee->branch ?? NULL;

        if ($selected_branch) {
            $branch_id = $selected_branch->id;
        } else {
            $branch_id = NULL;
        }

        $sales = User::whereRelation('roles', 'title', 'Sales')->get()->last();

        $dailyPayments = Payment::whereHas('account', function ($q) use ($branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereHas('invoice', function ($q) {
            $q->whereHas('membership');
        })
            ->with('invoice')
            ->whereDate('created_at', date('Y-m-d'))->sum('amount');

        $dailyExternalPayments = ExternalPayment::whereDate('created_at', date('Y-m-d'))->sum('amount');

        $dailyExternalPayments = ExternalPayment::whereHas('account', function ($q) use ($branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereDate('created_at', date('Y-m-d'))->sum('amount');

        $daily_income = $dailyPayments + $dailyExternalPayments;

        ////////////////////////////////

        $daily_expenses = Expense::whereDate('date', date('Y-m-d'))->sum('amount');

        $daily_loans    = Loan::whereDate('created_at', date('Y-m-d'))->sum('amount');

        $daily_refunds = Refund::whereStatus('confirmed')->whereDate('created_at', date('Y-m-d'))->sum('amount');

        $daily_outcome = $daily_expenses + $daily_refunds + $daily_loans;



        $daily_refunds = Refund::whereHas('account', function ($q) use ($branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->whereStatus('confirmed')
            ->whereDate('created_at', date('Y-m-d'))
            ->sum('amount');

        $daily_expenses = Expense::whereHas('account', function ($q) use ($branch_id) {
            $q->whereManager(false)
                ->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->whereDate('date', date('Y-m-d'))
            ->sum('amount');

        $daily_loans = Loan::whereHas('account', function ($q) use ($branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->whereDate('created_at', date('Y-m-d'))
            ->sum('amount');

        $daily_outcome = $daily_expenses + $daily_refunds + $daily_loans;

        ////////////////////////////////

        $daily_net = $daily_income - $daily_outcome;

        //////////////////////////////////////////////////////////////////////////

        $monthlyPayments = Payment::whereHas('account', function ($q) use ($branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereHas('invoice', function ($q) {
            $q->where('status', '!=', 'refund')->whereHas('membership');
        })
            ->with('invoice')
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->sum('amount');

        $monthlyExternalPayments = ExternalPayment::whereHas('account', function ($q) use ($branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->sum('amount');

        $monthly_income = $monthlyPayments + $monthlyExternalPayments;

        //////////////////////////

        // $monthly_expenses = Expense::whereYear('date', date('Y-m'))->whereMonth('date', date('m'))->sum('amount');

        // $refunds = Refund::whereStatus('confirmed')->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('amount');

        // $monthly_loans    = Loan::whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('amount');

        // $monthly_outcome = $monthly_expenses + $refunds + $monthly_loans;


        $refunds = Refund::whereHas('account', function ($q) use ($branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->whereStatus('confirmed')
            ->whereYear('created_at', date('Y-m'))
            ->whereMonth('created_at', date('m'))
            ->sum('amount');

        $monthly_expenses = Expense::whereHas('account', function ($q) use ($branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->whereYear('date', date('Y-m'))->whereMonth('date', date('m'))->sum('amount');

        $monthly_loans = Loan::whereHas('account', function ($q) use ($branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('amount');


        $monthly_outcome = $monthly_expenses + $refunds + $monthly_loans;

        //////////////////////////

        $monthly_net = $monthly_income - $monthly_outcome;

        //////////////////////////////////////////////////////////////////////////

        $daily_attendances = MembershipAttendance::whereDate('created_at', date('Y-m-d'))->count();

        $expired_attendances = MembershipAttendance::with('membership')->whereDate('created_at', date('Y-m-d'))->whereHas('membership', function ($q) {
            $q->where('end_date', '>', date('Y-m-d'));
        })->count();

        //////////////////////////////////////////////////////////////////////////

        $transactions = Transaction::whereDate('created_at', date('Y-m-d'))->latest()->get();

        $services = Service::withCount('memberships')->orderBy('memberships_count', 'desc')->paginate(5);

        $pricelists = Pricelist::withCount('memberships')->orderBy('memberships_count', 'desc')->paginate(5);

        $schedules = Schedule::with(['trainer', 'timeslot'])
            ->whereHas('schedule_main', function ($q) {
                $q->whereHas('schedule_main_group', fn ($y) => $y->whereStatus('active'))
                    // ->whereBranchId(
                    //     Auth()->user()->branch_id
                    // )
                ;
            })
            ->whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))
            ->get()
            ->groupBy('timeslot_id');

        $timeslots = Timeslot::orderBy('from', 'asc')->get();

        $today_attendants = MembershipAttendance::with([
            'membership' => fn ($q) => $q->whereHas('member')
                ->withCount('attendances')
        ])->whereHas('membership')->whereDate('created_at', date('Y-m-d', strtotime(now())))
            ->orderBy('id')->get();
        // if(Auth::user()->id == 1 ){
        //     dd(MembershipAttendance::with([
        //         'membership' => fn($q) => $q->whereHas('member')
        //                             ->withCount('attendances')
        //         ])->whereHas('membership')->whereDate('created_at', date('Y-m-d', strtotime(now())))
        //         ->orderBy('id')->whereId('13427')->first());
        // }




        $total_targets = Employee::whereHas('user', function ($q) {
            $q->whereHas('roles', function ($s) {
                $s->where('title', 'Sales');
            });
        })->sum('target_amount');

        // Employees Schedule
        $days = ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

        $employees_schedules = collect([]);
        foreach ($days as $day) {
            $emp_schedules = EmployeeSchedule::with('employee')->whereHas('employee', function ($q) {
                $q->whereStatus('active');
            })->where('is_offday', 0)->where('day', $day)->get();
            $employees_schedules[$day] = collect([]);
            foreach ($emp_schedules as $employee_schedule) {
                $employees_schedules[$day]->push([
                    'employee_name'     => $employee_schedule->employee->name,
                    'from'              => $employee_schedule->from,
                    'to'                => $employee_schedule->to,
                    'employee_id'       => $employee_schedule->employee_id
                ]);
            }
        }

        $overdue_reminders   = Reminder::whereHas('lead')->whereDate('due_date', '<', date('Y-m-d'))->get();
        $today_reminders     = Reminder::whereHas('lead')->whereDate('due_date', date('Y-m-d'))->get();
        $upcomming_reminders = Reminder::whereHas('lead')->whereDate('due_date', '>', date('Y-m-d'))->get();


        return view('home', compact(
            'daily_income',
            'daily_expenses',
            'employees_schedules',
            'days',
            'daily_net',
            'timeslots',
            'monthly_income',
            'monthly_expenses',
            'monthly_net',
            'daily_attendances',
            'expired_attendances',
            'transactions',
            'today_attendants',
            'services',
            'pricelists',
            'schedules',
            'total_targets',
            'daily_outcome',
            'monthly_outcome',
            'overdue_reminders',
            'today_reminders',
            'upcomming_reminders',
        ));
    }

    public function accountant()
    {
        $dailyPayments = Payment::whereHas('invoice', function ($q) {
            $q = $q->where('status', '!=', 'refund');
        })->whereDate('created_at', date('Y-m-d'))->sum('amount');

        $dailyExternalPayments = ExternalPayment::whereDate('created_at', date('Y-m-d'))->sum('amount');

        $daily_income = $dailyPayments + $dailyExternalPayments;

        $daily_expenses = Expense::whereDate('created_at', date('Y-m-d'))->sum('amount');

        $daily_net = $daily_income - $daily_expenses;

        //////////////////////////////////////////////////////////////////////////
        $monthlyPayments = Payment::whereHas('invoice', function ($q) {
            $q = $q->where('status', '!=', 'refund');
        })->whereYear('created_at', date('Y-m'))->whereMonth('created_at', date('m'))->sum('amount');

        $monthlyExternalPayments = ExternalPayment::whereYear('created_at', date('Y-m'))->whereMonth('created_at', date('m'))->sum('amount');

        $monthly_income = $monthlyPayments + $monthlyExternalPayments;

        $monthly_expenses = Expense::whereYear('created_at', date('Y-m'))->whereMonth('created_at', date('m'))->sum('amount');

        $monthly_net = $monthly_income - $monthly_expenses;

        ///////////////////////////////////////////////////////////////////////////

        $daily_attendances = MembershipAttendance::whereDate('created_at', date('Y-m-d'))->count();

        $expired_attendances = MembershipAttendance::with('membership')->whereDate('created_at', date('Y-m-d'))->whereHas('membership', function ($q) {
            $q->where('end_date', '>', date('Y-m-d'));
        })->count();

        //////////////////////////////////////////////////////////////////////////

        $transactions = Transaction::whereDate('created_at', date('Y-m-d'))->latest()->get();

        $services = Service::withCount('memberships')->orderBy('memberships_count', 'desc')->paginate(5);

        $pricelists = Pricelist::withCount('memberships')->orderBy('memberships_count', 'desc')->paginate(5);

        $schedules = Schedule::with(['trainer', 'timeslot'])->where('date', date('Y-m'))->get()->groupBy('timeslot_id');

        $timeslots = Timeslot::orderBy('from', 'asc')->get();

        $today_attendants = MembershipAttendance::with([
            'membership' => fn ($q) => $q->whereHas('member')
                ->withCount('attendances')
        ])->whereHas('membership')->whereDate('created_at', date('Y-m-d', strtotime(now())))
            ->orderBy('id')->get();

        $total_targets = Employee::sum('target_amount');

        return view('home', compact(
            'daily_income',
            'daily_expenses',
            'daily_net',
            'timeslots',
            'monthly_income',
            'monthly_expenses',
            'monthly_net',
            'daily_attendances',
            'expired_attendances',
            'transactions',
            'today_attendants',
            'services',
            'pricelists',
            'schedules',
            'total_targets'
        ));
    }

    public function sales()
    {
        $leadReminders = Reminder::with(['lead' => fn ($q) => $q->whereType('lead')])
            ->whereHas('lead', function ($q) {
                $q->whereType('lead');
            })
            ->whereNotIn('type', ['pt_session'])
            ->whereUserId(Auth()->id())
            ->get();

        $memberReminders = Reminder::with(['lead' => fn ($q) => $q->whereType('member')])
            ->whereHas('lead', function ($q) {
                $q->whereType('member');
            })
            ->whereNotIn('type', ['pt_session'])
            ->whereUserId(Auth()->id())
            ->get();

        $overdue_reminders   = Reminder::whereHas('lead')
            ->whereDate('due_date', '<', date('Y-m-d'))
            ->whereNotIn('type', ['pt_session'])
            ->whereUserId(Auth()->id())
            ->get();

        $today_reminders     = Reminder::whereHas('lead')
            ->whereDate('due_date', date('Y-m-d'))
            ->whereNotIn('type', ['pt_session'])
            ->whereUserId(Auth()->id())
            ->get();

        $upcomming_reminders = Reminder::whereHas('lead')
            ->whereDate('due_date', '>', date('Y-m-d'))
            ->whereNotIn('type', ['pt_session'])
            ->whereUserId(Auth()->id())
            ->get();

        $histories = LeadRemindersHistory::whereUserId(Auth()->id())
            ->whereDate('action_date', date('Y-m-d'))
            ->whereNotIn('type', ['pt_session'])
            ->get();

        $memberships = Membership::whereSalesById(Auth()->id())
            ->whereDate('created_at', date('Y-m-d'))
            ->get();

        $duePayments = Invoice::whereSalesById(Auth()->id())
            ->whereStatus('partial')
            ->get();

        $payments = Payment::whereHas('invoice', function ($q) {
            $q = $q->where('status', '!=', 'refund');
        })
            ->with([
                'sales_by', 'account', 'invoice', 'invoice.membership', 'invoice.membership.service_pricelist'
            ])
            ->whereSalesById(Auth()->user()->id)
            ->whereHas('invoice', function ($x) {
                $x->where('status', '!=', 'refund')
                    ->whereHas('membership', function ($i) {
                        $i->whereHas('service_pricelist', function ($i) {
                            $i->whereHas('service', function ($q) {
                                $q->whereSalesCommission(1);
                            });
                        });
                    });
            })
            ->whereDate('created_at', date('Y-m-d'))->get();


        $today_attendants = MembershipAttendance::with([
            'membership' => fn ($q) => $q->whereHas('member')
                ->withCount('attendances')
        ])->whereHas('membership')->whereDate('created_at', date('Y-m-d', strtotime(now())))
            ->orderBy('id')->get();

        $paymentsInMonth = Payment::whereHas('invoice', function ($q) {
            $q = $q->where('status', '!=', 'refund');
        })->whereHas('invoice', function ($x) {
            $x->where('status', '!=', 'refund')
                ->whereHas('membership', function ($i) {
                    $i->whereHas('service_pricelist', function ($i) {
                        $i->whereHas('service', function ($q) {
                            $q->whereSalesCommission(1);
                        });
                    });
                });
        })->with(['sales_by', 'account'])->whereSalesById(Auth()->user()->id)->whereYear('created_at', date('Y-m'))->whereMonth('created_at', date('m'));

        $target = 0;
        $achieved = 0;
        if (isset(Auth()->user()->sales_tier->sales_tier)) {
            $target = Auth()->user()->employee->target_amount;

            $achieved = $paymentsInMonth->sum('amount');

            $achieved_per = round(($paymentsInMonth->sum('amount') / Auth()->user()->employee->target_amount) * 100);

            $sales_commission = ($paymentsInMonth
                ->sum('amount') * Auth()->user()->sales_tier->sales_tier->sales_tiers_ranges()
                ->where('range_from', '<=', $achieved_per)
                ->orderBy('range_from', 'desc')
                ->first()
                ->commission) / 100;

            $sales_commission_per = (Auth()->user()->sales_tier->sales_tier->sales_tiers_ranges()->where('range_from', '<=', $achieved_per)->orderBy('range_from', 'desc')->first()->commission ?? 0);
        }

        $pending = $target - $achieved;
        
        if ($achieved >= $target) {
            $pendingTarget = " Achieved Target !";
        } else {
            $pendingTarget = number_format($pending);
        }

        $monthly_memberships = Membership::whereSalesById(Auth()->user()->id)->whereYear('created_at', date('Y-m'))->whereMonth('created_at', date('m'))->count();

        $schedules = Schedule::with(['trainer', 'timeslot'])->where('date', date('Y-m'))->get()->groupBy('timeslot_id');

        $timeslots = Timeslot::orderBy('from', 'asc')->get();

        // $reminders_sources = Reminder::with([
        //                                     'lead.source',
        //                                     'lead',
        //                                     'membership.invoice' => fn($q) => $q->withSum('payments','amount'),
        //                                     'membership.service_pricelist'
        //                                 ])
        //                                 ->whereUserId(Auth()->user()->id)
        //                                 ->whereHas('lead')
        //                                 ->whereDate('created_at',date('Y-m-d'))
        //                                 ->get()
        //                                 ->groupBy('lead.source.name');

        $reminders_sources = LeadRemindersHistory::with([
            'lead.source',
            'lead',
            'membership.invoice' => fn ($q) => $q->withSum('payments', 'amount'),
            'membership.service_pricelist'
        ])
            ->whereUserId(Auth()->user()->id)
            ->whereHas('lead')
            ->whereDate('created_at', date('Y-m-d'))
            ->get()
            ->groupBy('lead.source.name');


        $latest_leads = Lead::with(['memberships', 'invoices.payments', 'source'])
            ->where('branch_id', Auth()->user()->employee->branch_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->latest()
            ->get()
            ->groupBy('source.name');

        if (isset(Auth()->user()->sales_tier->sales_tier)) {
            return view('home', compact('leadReminders', 'today_attendants', 'memberReminders', 'memberships', 'payments', 'target', 'achieved', 'pendingTarget', 'paymentsInMonth', 'monthly_memberships', 'achieved_per', 'sales_commission', 'sales_commission_per', 'schedules', 'timeslots', 'duePayments', 'overdue_reminders', 'today_reminders', 'upcomming_reminders', 'histories', 'reminders_sources', 'latest_leads'));
        } else {
            return view('home', compact('leadReminders', 'today_attendants', 'memberReminders', 'memberships', 'payments', 'target', 'achieved', 'pendingTarget', 'paymentsInMonth', 'monthly_memberships', 'schedules', 'timeslots', 'duePayments', 'overdue_reminders', 'today_reminders', 'upcomming_reminders', 'histories', 'reminders_sources', 'latest_leads'));
        }
    }

    public function trainerOld()
    {
        $memberships = Membership::whereTrainerId(Auth()->user()->id)->whereYear('created_at', date('Y-m'))->whereMonth('created_at', date('m'))->get();

        $payments = 0;
        foreach ($memberships as $key => $membership) {
            $payments += $membership->payments->sum('amount');
        }

        // $attendances = TrainerAttendant::whereTrainerId(Auth()->user()->id)->whereYear('created_at',date('Y-m'))->whereMonth('created_at',date('m'))->get();
        $attendances = MembershipAttendance::whereHas('membership', function ($q) {
            $q = $q->where('trainer_id', auth()->id());
        })->whereYear('created_at', date('Y-m'))->whereDate('created_at', date('Y-m-d'))->latest()->get();

        $daily_attendances = MembershipAttendance::whereHas('membership', function ($q) {
            $q = $q->where('trainer_id', auth()->id());
        })->whereDate('created_at', date('Y-m-d'))->latest()->get();

        $revenue = 0;

        // Calculate revenue && Commission value
        foreach (auth()->user()->trainer_memberships as $membership) {
            $revenue += $membership->payments->sum('amount');
        }

        $commission = (isset(auth()->user()->sales_tier->sales_tier) && auth()->user()->sales_tier->sales_tier->sales_tiers_ranges()->where('range_from', '<=', $revenue)) ? auth()->user()->sales_tier->sales_tier->sales_tiers_ranges()->where('range_from', '<=', $revenue)->latest()->first()->commission : 0;


        // Commission value
        $commission_value = 0;

        foreach (auth()->user()->trainer_memberships as $ms) {
            if ($commission > 0) {
                if ($ms->invoice) {
                    if ($ms->service_pricelist->session_count != 0) {
                        $commission_value +=  $ms->member->trainer_attendants()->count() * ($ms->invoice->net_amount / $ms->service_pricelist->session_count);
                    } else {
                        $commission_value +=  0;
                    }
                }
            }
        }

        $sessions_count = 0;
        $sessions = auth()->user()->sessions()->get()->groupBy(['schedule_id', function ($item) {
            return $item->created_at->format('Y-m-d');
        }]);

        $monthly_memberships = Membership::whereSalesById(Auth()->user()->id)->whereYear('created_at', date('Y-m'))->whereMonth('created_at', date('m'))->count();

        $scheduales = Schedule::whereTrainerId(auth()->user()->id)->where('day', date('D'))->get();

        return view('home', compact('memberships', 'attendances', 'revenue', 'commission', 'commission_value', 'sessions_count', 'sessions', 'payments', 'scheduales', 'daily_attendances'));
    }




    public function receptionist()
    {
        $memberships = Membership::whereDate('created_at', date('Y-m-d'))->get();

        $payments = Payment::whereHas('invoice', function ($q) {
            $q = $q->where('status', '!=', 'refund');
        })->whereDate('created_at', date('Y-m-d'))->get();

        $attendances = MembershipAttendance::with(['membership' => fn ($q) => $q->whereHas('member')])->whereDate('created_at', date('Y-m-d', strtotime(now())))->get();

        $today_attendants = MembershipAttendance::with([
            'membership' => fn ($q) => $q->whereHas('member')
                ->withCount('attendances')
        ])->whereHas('membership')->whereDate('created_at', date('Y-m-d', strtotime(now())))
            ->orderBy('id')->get();

        $schedules = Schedule::with(['trainer', 'timeslot'])->where('date', date('Y-m'))->get()->groupBy('timeslot_id');

        $timeslots = Timeslot::orderBy('from', 'asc')->get();

        return view('home', compact('memberships', 'payments', 'attendances', 'today_attendants', 'schedules', 'timeslots'));
    }

    public function superVisor()
    {
        $dailyPayments = Payment::whereHas('invoice', function ($q) {
            $q = $q->where('status', '!=', 'refund');
        })->with(['invoice', 'invoice.membership', 'invoice.membership.service_pricelist', 'invoice.membership.service_pricelist.service', 'invoice.membership.service_pricelist.service.service_type' => fn ($q) => $q->where('session_type', 'sessions')])->whereDate('created_at', date('Y-m-d'))->get();

        $payments = Payment::with(['invoice', 'invoice.membership', 'invoice.membership.service_pricelist', 'invoice.membership.service_pricelist.service', 'invoice.membership.service_pricelist.service.service_type' => fn ($q) => $q->where('session_type', 'sessions')])->get();

        $invoices = Invoice::with(['membership', 'membership.service_pricelist', 'membership.service_pricelist.service', 'membership.service_pricelist.service.service_type' => fn ($q) => $q->where('session_type', 'sessions')])->withSum('payments', 'amount')->latest()->get();

        return view('home', compact('invoices', 'dailyPayments', 'payments'));
    }

    public function fitnessManager()
    {
        $trainers = User::whereRelation('roles', 'title', 'Trainer')->pluck('name', 'id');
        $db_trainers = User::whereRelation('roles', 'title', 'Trainer')->get();
        $commission = collect([]);
        $pre_commission = collect([]);
        foreach ($db_trainers as $trainer) {
            $memberships = Membership::withCount('attendances')
                ->with(['service_pricelist', 'member'])
                ->whereTrainerId($trainer->id)
                ->whereHas('invoice')
                ->whereMonth('start_date', date('m'))
                ->whereYear('start_date', date('Y'))->get();
            $total = 0;
            $total_attendance = 0;
            $totalInvoicesAmount = 0;

            foreach ($memberships as $membership) {
                $attendance_count = $membership->attendances_count;
                $total_attendance += $attendance_count;
                $total += ($membership->invoice->payments->sum('amount') / ($membership->service_pricelist->session_count == 0 ? 1 : $membership->service_pricelist->session_count)) * $attendance_count;
                $totalInvoicesAmount += $membership->invoice()->sum('net_amount');
            }

            $target_amount_percentage = isset($trainer->employee) && $trainer->employee->target_amount != NULL && $trainer->employee->target_amount > 0 ? ($total / $trainer->employee->target_amount) * 100 : 0;

            $db_sales_tier = SalesTier::whereHas('sales_tiers_users', fn ($q) => $q->where('user_id', $trainer->id))->where('type', 'trainer')->where('month', date('Y-m'))->first();
            if ($db_sales_tier) {
                $ranges = $db_sales_tier->sales_tiers_ranges()->where('range_to', '>', $target_amount_percentage)->where('range_from', '<=', $target_amount_percentage)->first();
            }
            $commission->push([
                'sales_tier'            => isset($ranges) ? $ranges->sales_tier->name : '',
                'commission_value'      => isset($ranges) ? $ranges->commission : '0',
                'commission'            => isset($ranges) ? $total * ($ranges->commission / 100) : '',
                'sales_tier_month'      => isset($ranges) ? date('F', strtotime($ranges->sales_tier->month)) : '',
                'trainer_id'            => $trainer->id,
                'total'                 => $total,
                'totalInvoices'         => $totalInvoicesAmount
            ]);

            $previous_memberships = Membership::whereHas('attendances', fn ($q) => $q->whereMonth('created_at', date('m')))->withCount('attendances')->whereTrainerId($trainer->id)->whereMonth('start_date', '<', date('m'))->get();

            $dates = [];

            $pre_total = 0;
            $pre_total_attendance = 0;

            foreach ($previous_memberships as $pre_membership) {
                array_push($dates, date('Y-m', strtotime($pre_membership->start_date)));
                $pre_attendance_count = $pre_membership->attendances_count;
                $pre_total_attendance += $pre_attendance_count;
                $pre_total += ($pre_membership->invoice->payments->sum('amount') / ($pre_membership->service_pricelist->session_count == 0 ? 1 : $pre_membership->service_pricelist->session_count)) * $pre_attendance_count;
            }
            $pre_sales_tier = SalesTier::whereHas('sales_tiers_users', function ($q) use ($trainer) {
                $q = $q->where('user_id', $trainer->id);
            })->get();
            //->whereIn('month', $dates)
            $previous_months_commissions = 0;
            if ($pre_sales_tier->isNotEmpty()) {
                foreach ($pre_sales_tier as $pre_st) {
                    foreach ($pre_st->sales_tiers_ranges()->where('range_to', '>', $total)->where('range_from', '<=', $total)->get() as $sales_tier_range) {
                        $previous_months_commissions += ($pre_total * ($sales_tier_range->commission / 100));
                        $pre_commission->push([
                            'trainer_id'                    => $trainer->id,
                            'sales_tier'                    => $pre_st->name,
                            'commission_value'              => $sales_tier_range->commission,
                            'pre_month_commission'          => $pre_total * ($sales_tier_range->commission / 100),
                            'sales_tier_month'              => date('F', strtotime($pre_st->month)),
                            'previous_months_commissions'   => $previous_months_commissions,
                            'pre_total'                     => $pre_total
                        ]);
                    }
                }
            }
        }

        return view('home', [
            'trainers'                      => $trainers,
            'commission'                    => $commission,
            'pre_commission'                => $pre_commission
        ]);
    }

    public function sales_manager()
    {
        $branch_id = Auth()->user()->employee ? Auth()->user()->employee->branch_id : NULL;
        $date = date('Y-m');

        $overdue_reminders   = Reminder::whereHas('lead')
            ->whereHas('user.employee', fn ($q) => $q->whereBranchId($branch_id))
            ->whereDate('due_date', '<', date('Y-m-d'))
            ->get();

        $today_reminders     = Reminder::whereHas('lead')
            ->whereHas('user.employee', fn ($q) => $q->whereBranchId($branch_id))
            ->whereDate('due_date', date('Y-m-d'))
            ->get();

        $upcomming_reminders = Reminder::whereHas('lead')
            ->whereHas('user.employee', fn ($q) => $q->whereBranchId($branch_id))
            ->whereDate('due_date', '>', date('Y-m-d'))
            ->get();

        // ===============================
        $sales = User::with([
            'memberships' => fn ($q) => $q->where('status', '!=', 'refunded')
                ->whereHas('invoice', function ($x) {
                    $x->where('status', '!=', 'refund');
                })
                ->whereHas('service_pricelist', function ($i) {
                    $i->whereHas('service', function ($q) {
                        $q->whereSalesCommission(1);
                    });
                })
                ->whereYear('created_at', date('Y-m', strtotime($date)))
                ->whereMonth('created_at', date('m', strtotime($date))),
            'payments'    => fn ($i) => $i->whereHas('invoice', function ($x) {
                $x->where('status', '!=', 'refund')
                    ->whereHas('membership', function ($i) {
                        $i->where('status', '!=', 'refunded')
                            ->whereHas('service_pricelist', function ($i) {
                                $i->whereHas('service', function ($q) {
                                    $q->whereSalesCommission(1);
                                });
                            });
                    });
            })
                ->whereYear('created_at', date('Y-m', strtotime($date)))
                ->whereMonth('created_at', date('m', strtotime($date)))
                ->get()
        ])
            ->withCount([
                'memberships' => fn ($q) => $q
                    ->where('status', '!=', 'refunded')
                    ->whereHas('invoice', function ($x) {
                        $x->where('status', '!=', 'refund');
                    })
                    ->whereHas('service_pricelist', function ($i) {
                        $i->whereHas('service', function ($q) {
                            $q->whereSalesCommission(1);
                        });
                    })
                    ->whereYear('created_at', date('Y-m', strtotime($date)))
                    ->whereMonth('created_at', date('m', strtotime($date)))
            ])
            ->withSum([
                'payments'
                => fn ($i) => $i->whereHas('invoice', function ($x) {
                    $x->where('status', '!=', 'refund')
                        ->whereHas('membership', function ($i) {
                            $i->where('status', '!=', 'refunded')
                                ->whereHas('service_pricelist', function ($i) {
                                    $i->whereHas('service', function ($q) {
                                        $q->whereSalesCommission(1);
                                    });
                                });
                        });
                })
                    ->whereYear('created_at', date('Y-m', strtotime($date)))
                    ->whereMonth('created_at', date('m', strtotime($date)))
            ], 'amount')
            ->whereRelation('roles', 'title', 'Sales')
            ->whereHas('employee', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
            ->get();

        $due = [];
        $collected = [];
        foreach ($sales as $key => $sale) {
            foreach ($sale->memberships as $membership) {
                // if (isset($membership->invoice)) {
                $due[$key] = $membership->invoice->net_amount;
                // }
            }
        }

        // ======================================
        $offers = Payment::with([
                'invoice' => fn ($q) => $q->withSum('payments', 'amount'),
                'invoice.membership',
                'invoice.membership.service_pricelist'
            ])
            ->whereHas('invoice', function ($q) use ($branch_id) {
                $q->where('status', '!=', 'refund')
                    ->whereHas('sales_by.employee',fn($q) => $q->whereBranchId($branch_id))
                    ->whereHas('membership.service_pricelist.service.service_type', function ($x) {
                        $x->whereMainService(true)->where('is_pt', false);
                    });
            })
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->get()
            ->groupBy('invoice.membership.service_pricelist.name');

        // ========================================

        $payments = Payment::whereHas('invoice', function ($y) use ($branch_id) {
                    $y->where('status', '!=', 'refund')
                        ->whereHas('sales_by.employee',fn($q) => $q->whereBranchId($branch_id))
                        ->whereHas('membership', function ($i) {
                            $i->where('status', '!=', 'refunded')
                                ->whereHas('service_pricelist.service', function ($x) { 
                                    $x->whereSalesCommission(1)->whereHas('service_type',fn($y) => $y->where('is_pt',false));
                                });
                        });
                })
                // ->whereIn('invoice_id',$invoices->pluck('id')->toArray())
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->get();

        $target = 0;
        $achieved = 0;

        foreach ($sales as $key => $sale) {
            if (isset($sale->sales_tier->sales_tier)) {
                $target += ($sale->employee->target_amount ?? 1);

                $achieved = $payments->sum('amount');

                $achieved_per = round(($payments->sum('amount') / $target) * 100);

                $sales_commission = ($payments
                    ->sum('amount') * $sale->sales_tier->sales_tier->sales_tiers_ranges()
                    ->where('range_from', '<=', $achieved_per)
                    ->orderBy('range_from', 'desc')
                    ->first()
                    ->commission) / 100;

                $sales_commission_per = ($sale->sales_tier->sales_tier->sales_tiers_ranges()->where('range_from', '<=', $achieved_per)->orderBy('range_from', 'desc')->first()->commission ?? 0);
            }

            $pending = $target - $achieved;

            $pendingTarget = $pending;
        }

        $invoices = Invoice::where('status', '!=', 'refund')
            ->whereHas('membership.service_pricelist.service', function ($q) {
                $q->whereSalesCommission(true)->whereHas('service_type',fn($x) => $x->where('is_pt',false));
            })
            ->whereHas('sales_by.employee',fn($q) => $q->whereBranchId($branch_id))
            // ->whereBranchId($branch_id)
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->get();

        $all_leads = Lead::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->whereBranchId($branch_id);

        $members = Lead::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->whereHas('memberships')
            ->whereBranchId($branch_id);

        $leads = $all_leads->count() - $members->count();

        $members_per = $members->count() > 0 ? ($members->count() / $all_leads->count()) * 100 : 0;

        $leads_per = $leads > 0 ? ($leads / $all_leads->count()) * 100 : 0;

        // ======================================

        $sales_reminders = User::whereRelation('roles', 'title', 'Sales')
            ->with([
                'reminders'             => fn ($q) => $q->whereDate('due_date', date('Y-m-d'))
                    ->whereNotIn('type', ['pt_session']),
                'reminders_histories'   => fn ($q) => $q->whereDate('due_date', date('Y-m-d'))
                    ->whereNotIn('type', ['pt_session']),
            ])
            ->whereHas('employee', fn ($q) => $q->whereStatus('active'))
            ->whereHas('reminders', function ($q) {
                $q->whereDate('due_date', date('Y-m-d'))->whereNotIn('type', ['pt_session']);
            })
            ->withCount([
                'reminders'             => fn ($q) => $q->whereDate('due_date', date('Y-m-d'))
                    ->whereNotIn('type', ['pt_session']),
                'reminders_histories'   => fn ($q) => $q->whereDate('due_date', date('Y-m-d'))
                    ->whereNotIn('type', ['pt_session']),
            ])
            ->get();

        $sources = Source::with([
            'leads'   => fn ($q) => $q->whereBranchId($branch_id)->whereYear('created_at', date('Y', strtotime($date)))->whereMonth('created_at', date('m', strtotime($date))),

            'members' => fn ($q) => $q->whereBranchId($branch_id)->whereYear('created_at', date('Y', strtotime($date)))->whereMonth('created_at', date('m', strtotime($date)))->whereHas('invoices', fn ($y) => $y->withSum('payments', 'amount'))
        ])
            ->withCount([
                'leads'   => fn ($q) => $q->whereBranchId($branch_id)->whereYear('created_at', date('Y', strtotime($date)))->whereMonth('created_at', date('m', strtotime($date))),

                'members' => fn ($q) => $q->whereBranchId($branch_id)->whereYear('created_at', date('Y', strtotime($date)))->whereMonth('created_at', date('m', strtotime($date)))->whereHas('invoices')
            ])
            ->get();

        $manager_target = Auth()->user()->employee->target_amount;

        $manager_achieved = Payment::whereHas(
                'invoice', fn ($q) => $q->where('status', '!=', 'refund')
                        ->whereHas('sales_by.employee',fn($q) => $q->whereBranchId($branch_id))
                        ->whereHas('membership', function ($i) {
                            $i->where('status', '!=', 'refunded')
                                ->whereHas('service_pricelist.service', function ($x) { 
                                    $x->whereSalesCommission(1)->whereHas('service_type',fn($y) => $y->where('is_pt',false));
                                });
                        })          
            )
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->sum('amount');

        $manager_pending = $manager_target - $manager_achieved;

        $manager_achieved_per = ($manager_achieved / $manager_target) * 100;

        $reminder_sales = User::whereRelation('roles', 'title', 'Sales')
            ->whereHas('employee', fn ($q) => $q->whereBranchId($branch_id)->whereStatus('active'))
            ->get();

        $reminders_sources = LeadRemindersHistory::with([
            'lead.source',
            'lead',
            'membership.invoice' => fn ($q) => $q->withSum('payments', 'amount'),
            'membership.service_pricelist'
        ])
            ->whereNotIn('type', ['pt_session'])
            ->whereHas('lead', fn ($q) => $q->whereBranchId($branch_id))
            ->whereDate('created_at', date('Y-m-d'))
            ->get()
            ->groupBy('lead.source.name');


        $latest_leads = Lead::with(['memberships', 'invoices.payments', 'source'])
            ->where('branch_id', $branch_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->latest()
            ->get()
            ->groupBy('source.name');

        return view('home', compact('offers', 'sales', 'overdue_reminders', 'today_reminders', 'upcomming_reminders', 'target', 'achieved', 'pendingTarget', 'achieved_per', 'sales_commission', 'payments', 'invoices', 'all_leads', 'members', 'leads', 'members_per', 'leads_per', 'sales_reminders', 'sources', 'manager_target', 'manager_achieved', 'manager_pending', 'manager_achieved_per', 'reminder_sales', 'reminders_sources', 'latest_leads'));
    }

    public function sales_director(Request $request)
    {
        $date = $request['date'] != NULL ? $request['date'] : date('Y-m');

        $branches = Branch::with(['sales_manager', 'payments'])
            ->withSum([
                'payments' => fn ($q) =>
                $q->whereYear('payments.created_at', date('Y', strtotime($date)))
                    ->whereMonth('payments.created_at', date('m', strtotime($date)))
                    ->whereHas('invoice', fn ($y) => $y->where('status', '!=', 'refund'))
            ], 'amount')
            ->get();

        return view('home', compact('branches'));
    }


    public function migrate()
    {
        dd(1);
        $url = 'https://zsheraton.dotapps.net/api/v1/login';
        $data = array('phone' => '01097465056', 'member_code' => 'password');
        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* Handle error */
        }
        foreach (json_decode($result) as $old_member) {
            $check_memebr = Lead::where('phone', '=', $old_member->phone)->get();

            if (count($check_memebr) > 0) {

                $member = Lead::where('phone', '=', $old_member->phone)->first();

                foreach ($old_member->memberships as $membership) {

                    $pricelist = Pricelist::whereName($membership->service_pricelist->name)->first() ?? Pricelist::first();

                    if ($membership->trainer) {
                        $trainer_by = User::whereName($membership->trainer->name)->first()->id ?? NULL;
                    } else {
                        $trainer_by = NULL;
                    }

                    $sales_by = User::whereName($membership->sales_by->name)->first() ?? User::first();

                    $new_membership = Membership::create([
                        'start_date'            => $membership->start_date,
                        'end_date'              => $membership->end_date,
                        'member_id'             => $member->id,
                        'trainer_id'            => $trainer_by,
                        'service_pricelist_id'  => $pricelist->id,
                        'notes'                 => $membership->notes,
                        'sales_by_id'           => $sales_by->id,
                        'notes'                 => $membership->notes,
                        'status'                => $membership->status,
                        'sales_by_id'           => $sales_by->id,
                        'sport_id'              => null,
                    ]);
                    foreach ($membership->attendances as $attendance) {

                        $new_attend = MembershipAttendance::create([
                            'sign_in'                   => $attendance->sign_in,
                            'sign_out'                  => $attendance->sign_out,
                            'membership_id'             => $new_membership->id,
                            'created_at'                => $attendance->created_at,
                            'membership_status'         => $new_membership->status
                        ]);
                    }

                    // foreach($membership->freezeRequests as $freeze){

                    //        $ff_created_by = User::whereName($freeze->created_by_id)->first() ?? User::first();
                    //         $new_freeze = FreezeRequest::create([
                    //                 'membership_id'     => $new_membership->id,
                    //                 'freeze'            => $freeze->freeze,
                    //                 'start_date'        => $freeze->start_date,
                    //                 'end_date'          => $freeze->end_date,
                    //                 'status'            => $freeze->status,
                    //                 'created_by_id'     => $ff_created_by->id,
                    //                 'is_retroactive'    => false
                    //             ]);

                    // }
                    $trackMembership = TrackMembership::create([
                        'membership_id'     => $new_membership->id,
                        'status'            => 'new'
                    ]);

                    $old_invoice = $membership->invoice;

                    $new_invoice = Invoice::create([
                        'discount'          => $old_invoice->discount,
                        'discount_notes'    => $old_invoice->discount_notes,
                        'service_fee'       => $old_invoice->service_fee,
                        'net_amount'        => $old_invoice->net_amount,
                        'membership_id'     => $new_membership->id,
                        'branch_id'         => $member->branch_id,
                        'sales_by_id'       => $sales_by->id,
                        'created_by_id'     => $sales_by->id,
                        'status'            => $old_invoice->status,
                        'created_at'        => $old_invoice->created_at
                    ]);

                    foreach ($old_invoice->payments as $old_payment) {

                        $account = Account::whereName($old_payment->account->name)->first() ?? Account::first();

                        $new_payment = Payment::create([
                            'account_id'        => $account->id,
                            'amount'            => $old_payment->amount,
                            'invoice_id'        => $new_invoice->id,
                            'sales_by_id'       => $sales_by->id,
                            'created_by_id'     => $sales_by->id,
                            'created_at'        => $old_payment->created_at
                        ]);

                        $transaction = Transaction::create([
                            'transactionable_type'  => 'App\\Models\\Payment',
                            'transactionable_id'    => $new_payment->id,
                            'amount'                => $old_payment->amount,
                            'account_id'            => $account->id,
                            'created_by'            => $sales_by->id,
                            'created_at'            => $old_payment->created_at
                        ]);
                    }
                }
            }
        }
        dd('Done');
    }

    public function fitness_manager(Request $request)
    {
        $branch_id = Auth()->user()->employee->branch->id;

        $non_pt_members = Membership::whereHas('member', fn($q) => $q->whereBranchId($branch_id))
            // ->whereDate('created_at','>=',date('Y-m-d',strtotime('-1 Month')))
            // ->whereDate('created_at','<=',date('Y-m-t'))
            ->whereDate('created_at', '>=', date('Y-m-01'))
            ->whereDate('created_at', '<=', date('Y-m-t'))
            ->whereHas('service_pricelist.service.service_type', fn ($q) => $q->where('is_pt', false))
            ->with([
                'member', 'trainer', 'service_pricelist', 'sales_by', 'service_pricelist.service', 'service_pricelist.service.service_type', 'member.branch', 'sport'
            ])
            ->withCount('attendances')
            ->withCount('trainer_attendances')
            ->latest()
            ->get();

        $pt_members =   Membership::whereHas('member', fn($q) => $q->whereBranchId($branch_id))
            // ->whereDate('created_at','>=',date('Y-m-d',strtotime('-1 Month')))
            ->whereDate('created_at', '>=', date('Y-m-01'))
            ->whereDate('created_at', '<=', date('Y-m-t'))
            ->whereHas('service_pricelist.service.service_type', fn ($q) => $q->where('is_pt', true))
            ->with(['member', 'trainer', 'service_pricelist', 'sales_by', 'service_pricelist.service', 'service_pricelist.service.service_type', 'member.branch', 'sport'])
            ->withCount('attendances')
            ->withCount('trainer_attendances')
            ->latest()
            ->get();

        $coaches = User::whereRelation('roles','title','Trainer')
                ->whereHas('employee',fn($q) => $q->whereStatus('active')->whereBranchId($branch_id))
                ->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $list = [];
        $amount = 0;
        $trainers =
            User::whereRelation('roles','title','Trainer')
                        ->whereHas('employee',fn($q) => $q->whereStatus('active')->whereBranchId($branch_id))
                        ->get();

        foreach ($trainers as $key => $trainer) {
            $memberships =      Membership::withCount('attendances')
                ->with([
                    'service_pricelist',
                    'member',
                    'invoice' => fn ($q) => $q->withSum([
                        'payments' => fn ($x) => $x->whereDate('created_at', '>=', date('Y-m-01'))
                            ->whereDate('created_at', '<=', date('Y-m-t'))
                    ], 'amount')
                ])
                ->whereTrainerId($trainer->id)
                ->whereHas(
                    'invoice',
                    fn ($q) => $q->where('status', '!=', 'refund')
                        ->whereHas(
                            'payments',
                            fn ($x) => $x
                                ->whereDate('created_at', '>=', date('Y-m-01'))
                                ->whereDate('created_at', '<=', date('Y-m-t'))
                        )
                )
                ->where('status', '!=', 'refund')
                // ->whereDate('created_at','>=',date('Y-m-d'))
                // ->whereDate('created_at','<=',date('Y-m-t'))
                ->get();

            $list[$key]['id']   = $trainer->id;
            $list[$key]['trainer_name'] = $trainer->name;
            foreach ($memberships as $membership) {
                // $amount += $membership->invoice->net_amount;
                $amount += $membership->invoice->payments_sum_amount;
            }
            $list[$key]['payments_amount'] = $amount;
            $amount = 0;
        }

        $reminders = [];
        $trainers_reminders = User::with('reminders')->whereRelation('roles','title','Trainer')
                        ->whereHas('employee',fn($q) => $q->whereStatus('active')->whereBranchId($branch_id))
                        ->pluck('name', 'id');

        foreach ($trainers_reminders as $key => $value) {
            $reminders[$key]['id']  = $key;
            $reminders[$key]['name'] = $value;
            $reminders[$key]['overdue_reminders'] =  Reminder::whereHas('lead')
                ->whereUserId($key)
                ->whereDate('due_date', '<', date('Y-m-d'))->count() ?? 0;
            $reminders[$key]['today_reminders'] =  Reminder::whereHas('lead')
                ->whereUserId($key)
                ->whereDate('due_date', date('Y-m-d'))->count() ?? 0;
            $reminders[$key]['upcomming_reminders'] =  Reminder::whereHas('lead')
                ->whereUserId($key)
                ->whereDate('due_date', '>', date('Y-m-d'))->count() ?? 0;
        }

        $trainers = User::whereRelation('roles', 'title', 'Trainer')
            ->whereHas(
                'employee',
                fn ($q) => $q->whereStatus('active')->whereBranchId($branch_id)
            )
            ->orderBy('name')
            ->pluck('name', 'id');

        $offers = Payment::with([
            'invoice' => fn ($q) => $q->withSum('payments', 'amount'),
            'invoice.membership',
            'invoice.membership.service_pricelist'
        ])
            ->whereHas('invoice', function ($q) use ($branch_id) {
                $q->where('status', '!=', 'refund')
                    ->whereHas('sales_by.employee',fn($q) => $q->whereBranchId($branch_id))
                    ->whereHas('membership', function ($x) {
                        $x->whereHas('service_pricelist', function ($s) {
                            $s->whereHas('service', function ($t) {
                                $t->whereHas('service_type', function ($w) {
                                    $w->where('is_pt', true);
                                });
                            });
                        });
                    });
            })
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->get()
            ->groupBy('invoice.membership.service_pricelist.name');

        return view('home', compact('pt_members', 'non_pt_members', 'coaches', 'list', 'reminders', 'trainers', 'offers'));
    }

    public  function trainer_reminders(User $trainer)
    {
        $overdue_reminders   = Reminder::whereHas('lead')
            ->whereUserId($trainer->id)
            ->whereDate('due_date', '<', date('Y-m-d'))->get();

        $today_reminders     = Reminder::whereHas('lead')
            ->whereUserId($trainer->id)
            ->whereDate('due_date', date('Y-m-d'))->get();

        $upcomming_reminders = Reminder::whereHas('lead')
            ->whereUserId($trainer->id)
            ->whereDate('due_date', '>', date('Y-m-d'))->get();

        return view('dashboard.ftiness_manager.trainers_reminders', compact('overdue_reminders', 'today_reminders', 'upcomming_reminders'));
    }


    public  function trainer_payments(User $trainer)
    {
        $memberships = Membership::withCount('attendances')
            ->with(['service_pricelist', 'member'])
            ->whereTrainerId($trainer->id)
            ->whereHas('invoice', fn ($q) => $q->where('status', '!=', 'refund'))
            ->where('status', '!=', 'refund')
            ->whereMonth('created_at', date('m', strtotime(date('Y-m-d'))))
            ->whereYear('created_at', date('Y', strtotime(date('Y-m-d'))))
            ->latest()
            ->get();

        return view('dashboard.trainers.index', compact('memberships'));
    }

    public function trainer()
    {
        $non_pt_members =
            Membership::whereHas('member', function ($q) {
                $q->whereBranchId(Auth()->user()->employee->branch_id);
            })->whereMonth('created_at', date('m'))
            ->whereHas('service_pricelist.service.service_type', fn ($q) => $q->where('is_pt', false))
            ->whereYear('created_at', date('Y'))
            ->with([
                'member', 'trainer', 'service_pricelist', 'sales_by', 'service_pricelist.service', 'service_pricelist.service.service_type', 'member.branch', 'sport'
            ])->where('assigned_coach_id', Auth()->user()->id)
            ->withCount('attendances')
            ->withCount('trainer_attendances')
            ->get();

        $pt_members =   Membership::whereHas('member', function ($q) {
            $q->whereBranchId(Auth()->user()->employee->branch_id);
        })->whereMonth('created_at', date('m'))
            ->whereHas('service_pricelist.service.service_type', fn ($q) => $q->where('is_pt', true))
            ->whereYear('created_at', date('Y'))
            ->with(['member', 'trainer', 'service_pricelist', 'sales_by', 'service_pricelist.service', 'service_pricelist.service.service_type', 'member.branch', 'sport'])
            ->where('trainer_id', Auth()->user()->id)
            ->withCount('attendances')
            ->withCount('trainer_attendances')
            ->get();

        $overdue_reminders   = Reminder::whereHas('lead')
            ->whereUserId(Auth()->user()->id)
            ->whereDate('due_date', '<', date('Y-m-d'))->get();

        $today_reminders     = Reminder::whereHas('lead')
            ->whereUserId(Auth()->user()->id)
            ->whereDate('due_date', date('Y-m-d'))->get();

        $upcomming_reminders = Reminder::whereHas('lead')
            ->whereUserId(Auth()->user()->id)
            ->whereDate('due_date', '>', date('Y-m-d'))->get();

        return view('home', compact('pt_members', 'non_pt_members', 'overdue_reminders', 'today_reminders', 'upcomming_reminders'));
    }

    public function default()
    {
        return view('home');
    }

    public function sitemap()
    {
        return view('admin.sitemap');
    }

    public function fixSales()
    {
        return 'fix Sales';
    }


    
}
