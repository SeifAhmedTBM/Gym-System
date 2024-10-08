<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ActionsReportExport;
use PDF;
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
use App\Models\Setting;
use App\Models\Reminder;
use App\Models\Schedule;
use App\Models\Timeslot;
use App\Models\Pricelist;
use App\Models\SalesTier;
use App\Models\Membership;
use App\Models\ServiceType;
use App\Models\SessionList;
use Illuminate\Support\Str;
use App\Models\RefundReason;
use App\Models\ScheduleMain;
use Illuminate\Http\Request;
use App\Models\FreezeRequest;
use App\Models\ExternalPayment;
use App\Models\ExpensesCategory;
use App\Models\TrainerAttendant;
use App\Services\TrainerService;
use Illuminate\Support\Facades\DB;
use App\Exports\ExportCoachesReport;
use App\Exports\MonthlyReportExport;
use App\Exports\TaxAccountantExport;
use App\Http\Controllers\Controller;
use App\Models\LeadRemindersHistory;
use App\Models\MembershipAttendance;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportRevenuesReport;
use App\Exports\ExportTrainersReport;
use App\Exports\FreezeRequestsExport;
use App\Models\ExternalPaymentCategory;
use Illuminate\Contracts\Session\Session;
use App\Exports\ExportRevenueDetailsReport;
use App\Exports\ExportAthletesInstructedReport;
use App\Exports\ExportCurrentMembershipsReport;
use App\Exports\ExportSessionAttendancesReport;
use App\Exports\ExportSessionsInstructedReport;
use App\Exports\ExportExpiredMembershipAttendancesReport;
use App\Exports\GuestLogExport;
use Carbon\Carbon;
use App\Services\SalesService;

class ReportController extends Controller
{
    public function revenue(Request $request)
    {
        $sessions_query = SessionList::query()->whereHas('trainer_attendants')->with('service');
        $session_ids = $sessions_query->pluck('id')->toArray();
        $sessions = $sessions_query->get();
        $schedules = Schedule::whereIn('session_id', $session_ids)->pluck('id')->toArray();
        $trainer_attendants = TrainerAttendant::with(['schedule', 'membership', 'membership.invoice', 'membership.service_pricelist'])
            ->whereHas('membership', fn ($q) => $q->whereHas('invoice'))
            ->whereIn('schedule_id', $schedules)
            ->when($request->input('month'), function ($query) use ($request) {
                $query->whereMonth('created_at', explode('-', $request->input('month'))[1])
                    ->whereYear('created_at', explode('-', $request->input('month'))[0]);
            })
            ->unless($request->input('month'), fn ($q) => $q->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y')))
            ->get();
        $report = [];
        foreach ($sessions as $sess) {
            $report[$sess->id] = [
                'session_id'        => $sess->id,
                'attendants'        => 0,
                'sessions_count'    => 0,
                'revenue'           => 0,
                'utilization_rate'  => $sess->ranking,
                'session'           => [
                    'name'          => $sess->name,
                    'color'         => $sess->color,
                    'max_capacity'  => $sess->max_capacity,
                    'ranking'       => $sess->ranking
                ]
            ];
        }
        foreach ($trainer_attendants as $trainer_attendant) {
            $report[$trainer_attendant->schedule->session_id]['attendants'] += 1;
            $report[$trainer_attendant->schedule->session_id]['revenue'] += $trainer_attendant->membership->invoice->net_amount / $trainer_attendant->membership->service_pricelist->session_count;
        }
        foreach ($sessions as $session) {

            $report[$session->id]['sessions_count'] = TrainerAttendant::whereIn('schedule_id', $session->schedules()->pluck('id')->toArray())
                ->when($request->input('month'), function ($query) use ($request) {
                    $query->whereMonth('created_at', explode('-', $request->input('month'))[1])
                        ->whereYear('created_at', explode('-', $request->input('month'))[0]);
                })
                ->unless($request->input('month'), fn ($q) => $q->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y')))
                ->distinct(DB::raw('DATE(created_at)'))->count();


            if ($report[$session->id]['attendants'] > 0) {
                $session->append(['ranking' => round(($report[$session->id]['attendants'] / ($report[$session->id]['sessions_count'] * $report[$session->id]['session']['max_capacity'])) * 100)]);
            }

            $report[$session->id]['utilization_rate'] = $report[$session->id]['attendants'] > 0 ? round(($report[$session->id]['attendants'] / ($report[$session->id]['sessions_count'] * $report[$session->id]['session']['max_capacity'])) * 100, 2) : 0;
        }
        return view('admin.reports.revenue', compact('sessions', 'report'));
    }

    public function coaches(Request $request)
    {
        $trainers = User::whereRelation('roles', 'title', 'Trainer')->whereHas('sessions')->get();
        $reports = collect([]);
        foreach ($trainers as $trainer) {
            $trainer_attendants = TrainerAttendant::query()
                ->with(['schedule', 'schedule.schedule_main', 'schedule.session', 'schedule.session.service', 'schedule.session.service.service_pricelist', 'membership', 'membership.service_pricelist', 'membership.invoice'])
                ->where('trainer_id', $trainer->id)
                ->when($request->input('month'), function ($query) use ($request) {
                    $query->whereMonth('created_at', explode('-', $request->month)[1])
                        ->whereYear('created_at', explode('-', $request->month)[0]);
                })
                ->unless($request->input('month'), function ($query) {
                    $query->whereMonth('created_at', date('m'))
                        ->whereYear('created_at', date('Y'));
                });

            $fixed_commissions = $trainer_attendants->whereHas('schedule.schedule_main', fn ($q) => $q->whereCommissionType('fixed'))->get();
            $fixed = 0;
            foreach ($fixed_commissions as $fixed_comm) {
                $fixed += $fixed_comm->schedule->schedule_main->commission_amount;
            }

            $sessions_instructed = $trainer_attendants->get([
                'trainer_id',
                'schedule_id',
                DB::raw('DATE(created_at) AS day')
            ])->unique('day')->count();

            $athletes_instructed = $trainer_attendants->get()->unique('member_id')->count();
            $revenue = 0;
            foreach ($trainer_attendants->get() as $attend) {
                if ($attend->membership != NULL && $attend->membership->service_pricelist->session_count != 0) {
                    $revenue += ($attend->membership->invoice->net_amount /
                        $attend->membership->service_pricelist->session_count);
                }
            }
            if ($sessions_instructed > 0) {
                $reports->push([
                    'trainer_name'                  => $trainer->name,
                    'trainer_id'                    => $trainer->id,
                    'fixed'                         => $fixed,
                    'sessions_instructed'           => $sessions_instructed,
                    'athletes_instructed'           => $athletes_instructed,
                    'revenue'                       => round($revenue)
                ]);
            }
        }
        return view('admin.reports.coaches', compact('trainers', 'reports'));
    }

    public function sessionAttendances($session_name)
    {
        $session = SessionList::with(['schedules', 'trainer_attendants', 'trainer_attendants.member'])->whereName($session_name)->firstOrFail();
        $no_of_attendances = [];
        $subscription = [];
        $cost_per_session = [];
        $revenue = [];
        foreach ($session->trainer_attendants as $key => $attendant) {
            if (count($attendant->member->memberships()->whereDate('end_date', '>=', date('Y-m-d'))->get()) > 0) {
                $no_of_attendances[$key] = $attendant->member->getMyAttendanceCount($session->schedules()->get('id'));
                $subscription[$key] = $attendant->member->memberships()->whereDate('end_date', '>=', date('Y-m-d'))->first()->service_pricelist->service->name;
                $cost_per_session[$key] = ($attendant->member->memberships()->whereDate('end_date', '>=', date('Y-m-d'))->first()->invoice->net_amount / $session->service->service_pricelist->session_count);
                $revenue[$key] = ($attendant->member->memberships()->whereDate('end_date', '>=', date('Y-m-d'))->first()->invoice->net_amount / $session->service->service_pricelist->session_count) * $attendant->member->getMyAttendanceCount($session->schedules()->get('id'));
            }
        }
        // $session = cache()->get('sessionAttendance');
        // $no_of_attendances = cache()->get('no_of_attendances');
        // $subscription = cache()->get('subscription');
        // $cost_per_session = cache()->get('cost_per_session');
        // $revenue = cache()->get('revenue');
        return view('admin.reports.session_attendance', compact('session', 'subscription', 'no_of_attendances', 'cost_per_session', 'revenue'));
    }

    public function scheduleTimeline()
    {
        $schedules = Schedule::with(['trainer', 'timeslot'])->where('date', date('Y-m'))->get()->groupBy('timeslot_id');
        $timeslots = Timeslot::orderBy('from', 'asc')->get();
        return view('admin.schedules.timeline', compact('schedules', 'timeslots'));
    }

    public function services(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $services = Pricelist::whereHas('memberships', function ($q) use ($date) {
            $q->whereYear('memberships.created_at', date('Y-m', strtotime($date)))
                ->whereMonth('memberships.created_at', date('m', strtotime($date)))
                ->where('status', '!=', 'refunded')
                ->whereHas('invoice', function ($i) use ($date) {
                    $i->where('status', '!=', 'refund')
                        ->whereHas('payments');
                });
        })->get();
        $report = collect([]);
        foreach ($services as $service) {
            $memberships = Membership::whereYear('created_at', date('Y-m', strtotime($date)))
                ->whereMonth('created_at', date('m', strtotime($date)))
                ->where('status', '!=', 'refunded')
                ->whereHas('invoice', function ($i) use ($date) {
                    $i->whereYear('created_at', date('Y-m', strtotime($date)))
                        ->whereMonth('created_at', date('m', strtotime($date)))
                        ->whereHas('payments');
                })
                ->where('service_pricelist_id', $service->id)
                ->get();
            $invoices = Invoice::where('status', '!=', 'refund')
                ->whereYear('created_at', date('Y', strtotime($date)))
                ->whereMonth('created_at', date('m', strtotime($date)))->withSum('payments', 'amount')->whereIn('membership_id', $memberships->pluck('id')->toArray())->pluck('id')->toArray();

            $payments = Payment::whereIn('invoice_id', $invoices)->sum('amount');
            $report->push([
                'service_name'      => $service->name,
                'memberships_count' => $memberships->count(),
                'payments'          => $payments
            ]);
        }


        return view('admin.reports.services', compact('services', 'report'));
    }

    public function offers(Request $request)
    {

        $date = isset($request->date) ? $request->date : date('Y-m');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $payments = Payment::with(['invoice', 'invoice.membership', 'invoice.membership.service_pricelist'])
            ->whereHas('invoice', function ($q) use ($date) {
                $q->where('status', '!=', 'refund')
                    ->whereHas('membership', function ($x) use ($date) {
                        $x->whereHas('service_pricelist');
                    });
            })
            ->whereYear('created_at', date('Y-m', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->whereHas('account', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
            ->get()
            ->groupBy('invoice.membership.service_pricelist.name');

        return view('admin.reports.offers', compact('payments', 'employee', 'branch_id'));
    }

    public function leadsSource(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $sources = Lead::whereType('lead')
            ->with('source')
            ->whereHas('source')
            ->whereYear('created_at', date('Y-m', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->when($branch_id, fn ($q) => $q->whereBranchId($branch_id))
            ->get()
            ->groupBy(['source.name']);

        return view('admin.reports.leadsSource', compact('sources', 'employee', 'branch_id'));
    }

    public function leadsSourceShow(Request $request,$name)
    {
        $source = Source::whereName($name)->first();

        $date = isset($request->date) ? $request->date : date('Y-m');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $leads = Lead::whereSourceId($source->id)
            ->whereType('lead')
            ->with('source')
            ->whereYear('created_at', date('Y-m', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->when($branch_id, fn ($q) => $q->whereBranchId($branch_id))
            ->get();

        return view('admin.reports.leads_source_show', compact('leads','source','employee', 'branch_id'));
    }

    public function membersSource(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $sources_members = Payment::with([
            'invoice',
            'invoice.membership' => fn ($q) => $q->with('member')
                ->whereHas('member', function ($m) use ($branch_id) {
                    $m->whereType('member')->when($branch_id, fn ($q) => $q->whereBranchId($branch_id));
                }),
            'invoice.membership.service_pricelist'
        ])
            ->whereHas('invoice', function ($i) use ($date) {
                $i->where('status', '!=', 'refund')
                    ->whereHas('membership', function ($m) use ($date) {
                        $m->whereHas('service_pricelist');
                    });
            })
            ->whereYear('created_at', date('Y-m', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->get()
            ->groupBy(['invoice.membership.member.source.name']);

        return view('admin.reports.membersSource', compact('sources_members', 'employee', 'branch_id'));
    }

    public function salesReport(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }
        $type = Auth()->user()->roles[0]->title;
        $sales = User::when($request['sales_by_id'],fn($q) => $q->whereId($request['sales_by_id']))
            ->with([
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
            ->whereHas('employee', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)));
        if($type == 'Sales'){
            $sales = $sales->where('id',Auth()->user()->id);
        }
                $sales = $sales->get();

        $due = [];
        $collected = [];
        foreach ($sales as $key => $sale) {
            foreach ($sale->memberships as $membership) {
                // if (isset($membership->invoice)) {
                $due[$key] = $membership->invoice->net_amount;
                // }
            }
        }

        return view('admin.reports.sales', compact('sales', 'due', 'collected', 'employee', 'branch_id'));
    }

    public function viewSalesReport(Request $request, $id)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $sale = User::with([
            'memberships' => fn ($q) => $q->whereHas('invoice')
                ->where('status', '!=', 'refunded')
                ->whereHas('service_pricelist', function ($i) {
                    $i->whereHas('service', function ($q) {
                        $q->whereSalesCommission(1);
                    });
                })
                ->where('sales_by_id', $id)
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
                ->get(),
            'trackMemberships'
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
            ->whereHas(
                'roles',
                function ($q) {
                    $q = $q->where('title', 'Sales');
                }
            )->findOrFail($id);

        $service_payments = Payment::with(['invoice', 'invoice.membership', 'invoice.membership.service_pricelist'])
            ->whereHas('invoice', function ($i) use ($date) {
                $i->where('status', '!=', 'refund')
                    ->whereHas('membership', function ($m) use ($date) {
                        $m->where('status', '!=', 'refunded')
                            ->whereHas('service_pricelist', function ($s) {
                                $s->whereHas('service', function ($y) {
                                    $y->whereSalesCommission(1);
                                });
                            });
                    });
            })
            ->whereSalesById($sale->id)
            ->whereYear('created_at', date('Y-m', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->get()
            ->groupBy(['invoice.membership.service_pricelist.name']);

        $sources_members = Payment::with([
            'invoice',
            'invoice.membership' => fn ($q) => $q->with('member')->whereHas('member', function ($m) {
                $m->whereType('member');
            }),
            'invoice.membership.service_pricelist'
        ])
            ->whereHas('invoice', function ($i) use ($date) {
                $i->where('status', '!=', 'refund')
                    ->whereHas('membership', function ($m) use ($date) {
                        $m->where('status', '!=', 'refunded')
                            ->whereHas('service_pricelist', function ($s) {
                                $s->whereHas('service', function ($y) {
                                    $y->whereSalesCommission(1);
                                });
                            });
                    });
            })
            ->whereSalesById($sale->id)
            ->whereYear('created_at', date('Y-m', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->get()
            ->groupBy(['invoice.membership.member.source.name']);

        return view('admin.reports.viewSales', compact('sale', 'service_payments', 'sources_members'));
    }

    public function freelancersReport(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $freelancers = User::with([
            'memberships' => fn ($q) => $q->whereHas('invoice')
                ->whereYear('created_at', date('Y-m', strtotime($date)))
                ->whereMonth('created_at', date('m', strtotime($date))),
            'payments'    => fn ($i) => $i->whereHas('invoice', function ($x) {
                $x->where('status', '!=', 'refund');
            })
                ->whereYear('created_at', date('Y-m', strtotime($date)))
                ->whereMonth('created_at', date('m', strtotime($date)))
                ->get()
        ])
            ->withCount([
                'memberships' => fn ($q) => $q
                    ->whereHas('invoice', function ($x) {
                        $x->where('status', '!=', 'refund');
                    })
                    ->whereYear('created_at', date('Y-m', strtotime($date)))
                    ->whereMonth('created_at', date('m', strtotime($date)))
            ])
            ->whereHas(
                'roles',
                function ($q) {
                    $q = $q->where('title', 'freelancer');
                }
            )->get();

        $due = [];
        $collected = [];
        foreach ($freelancers as $key => $freelancer) {
            foreach ($freelancer->memberships as $membership) {
                // if (isset($membership->invoice)) {
                $due[$key] = $membership->invoice->net_amount;
                // }
            }
        }
        return view('admin.reports.freelancers', compact('freelancers', 'due', 'collected'));
    }

    // public function trainersReport(Request $request)
    // {
    //     $date = isset($request->date) ? $request->date : date('Y-m');

    //     $employee = Auth()->user()->employee;

    //     if ($employee && $employee->branch_id != NULL) {
    //         $branch_id = $employee->branch_id;
    //     } else {
    //         $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
    //     }

    //     $trainers = User::when($request['trainer_id'],fn($q) => $q->whereId($request['trainer_id']))
    //         ->whereRelation('roles', 'title', 'Trainer')
    //         ->with(['employee', 'employee.branch'])
    //         ->whereHas('employee', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
    //         ->get();

    //     $db_trainers = User::when($request['trainer_id'],fn($q) => $q->whereId($request['trainer_id']))
    //         ->whereRelation('roles', 'title', 'Trainer')
    //         ->whereHas('employee', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
    //         ->get();

    //     $commission = collect([]);
    //     $pre_commission = collect([]);
    //     foreach ($db_trainers as $trainer) {
    //         $memberships = Membership::withCount('attendances')
    //             ->with([
    //                 'service_pricelist',
    //                 'member',
    //                 'invoice' => fn($q) => $q->withSum([
    //                     'payments' => fn($x) => $x->whereMonth('created_at', date('m', strtotime($date)))
    //                                         ->whereYear('created_at', date('Y', strtotime($date)))
    //                 ],'amount')
    //             ])
    //             ->whereTrainerId($trainer->id)
    //             ->whereHas(
    //                 'invoice',fn($q) => $q->where('status','!=','refund')
    //                     ->whereHas('payments',fn($x) => $x
    //                         ->whereMonth('created_at', date('m', strtotime($date)))
    //                         ->whereYear('created_at', date('Y', strtotime($date)))
    //                     )
    //             )
    //             ->where('status', '!=', 'refund')
    //             ->get();
    //         $total = 0;
    //         $total_attendance = 0;
    //         $totalInvoicesAmount = 0;

    //         foreach ($memberships as $membership) {
    //             $attendance_count = $membership->attendances_count;
    //             $total_attendance += $attendance_count;

    //             $total += ($membership->invoice->payments->sum('amount') / ($membership->service_pricelist->session_count == 0 ? 1 : $membership->service_pricelist->session_count)) * $attendance_count;

    //             $totalInvoicesAmount += $membership->invoice->payments_sum_amount;
    //         }

    //         $target_amount_percentage = isset($trainer->employee) && $trainer->employee->target_amount != NULL && $trainer->employee->target_amount > 0 ? ($total / $trainer->employee->target_amount) * 100 : 0;

    //         $db_sales_tier = SalesTier::whereHas('sales_tiers_users', fn ($q) => $q->where('user_id', $trainer->id))->where('type', 'trainer')->where('month', $date)->first();
    //         if ($db_sales_tier) {
    //             $ranges = $db_sales_tier->sales_tiers_ranges()->where('range_to', '>', $target_amount_percentage)->where('range_from', '<=', $target_amount_percentage)->first();
    //         }
    //         $commission->push([
    //             'sales_tier'            => isset($ranges) ? $ranges->sales_tier->name : '',
    //             'commission_value'      => isset($ranges) ? $ranges->commission : '0',
    //             'commission'            => isset($ranges) ? $total * ($ranges->commission / 100) : '',
    //             'sales_tier_month'      => isset($ranges) ? date('F', strtotime($ranges->sales_tier->month)) : '',
    //             'trainer_id'            => $trainer->id,
    //             'total'                 => $total,
    //             'totalInvoices'         => $totalInvoicesAmount
    //         ]);

    //         $previous_memberships = Membership::whereHas(
    //                 'attendances', fn ($q) => $q->whereDate('created_at','<',date('Y-m', strtotime($date))
    //             ))
    //             ->withCount('attendances')
    //             ->whereTrainerId($trainer->id)
    //             ->with([
    //                 'service_pricelist',
    //                 'member',
    //                 'invoice' => fn($q) => $q->withSum([
    //                     'payments' => fn($x) => $x->whereDate('created_at','<',date('Y-m-d',strtotime($date)))
    //                 ],'amount')
    //             ])
    //             ->whereHas(
    //                 'invoice',fn($q) => $q->where('status','!=','refund')
    //                     ->whereHas('payments',fn($x) => $x
    //                         ->whereDate('created_at','<', date('Y-m-d', strtotime($date)))
    //                     )
    //             )
    //             ->get();

    //         $dates = [];

    //         $pre_total = 0;
    //         $pre_total_attendance = 0;

    //         foreach ($previous_memberships as $pre_membership) {
    //             array_push($dates, date($date, strtotime($pre_membership->start_date)));
    //             $pre_attendance_count = $pre_membership->attendances_count;
    //             $pre_total_attendance += $pre_attendance_count;
    //             $pre_total += ($pre_membership->invoice->payments_sum_amount / ($pre_membership->service_pricelist->session_count == 0 ? 1 : $pre_membership->service_pricelist->session_count)) * $pre_attendance_count;
    //         }
    //         $pre_sales_tier = SalesTier::whereHas('sales_tiers_users', function ($q) use ($trainer) {
    //             $q = $q->where('user_id', $trainer->id);
    //         })->get();
    //         //->whereIn('month', $dates)
    //         $previous_months_commissions = 0;
    //         if ($pre_sales_tier->isNotEmpty()) {
    //             foreach ($pre_sales_tier as $pre_st) {
    //                 foreach ($pre_st->sales_tiers_ranges()->where('range_to', '>', $total)->where('range_from', '<=', $total)->get() as $sales_tier_range) {
    //                     $previous_months_commissions += ($pre_total * ($sales_tier_range->commission / 100));
    //                     $pre_commission->push([
    //                         'trainer_id'                    => $trainer->id,
    //                         'sales_tier'                    => $pre_st->name,
    //                         'commission_value'              => $sales_tier_range->commission,
    //                         'pre_month_commission'          => $pre_total * ($sales_tier_range->commission / 100),
    //                         'sales_tier_month'              => date('F', strtotime($pre_st->month)),
    //                         'previous_months_commissions'   => $previous_months_commissions,
    //                         'pre_total'                     => $pre_total
    //                     ]);
    //                 }
    //             }
    //         }
    //     }

    //     return view('admin.reports.trainers', [
    //         'trainers'                      => $trainers,
    //         'commission'                    => $commission,
    //         'pre_commission'                => $pre_commission,
    //         'branch_id'                     => $branch_id,
    //         'employee'                      => $employee,
    //     ]);
    // }

    public function trainersReport(Request $request)
    {
        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL)
        {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $trainer_service = new TrainerService;
        $trainers = $trainer_service->trainer_report($request,$branch_id);

        return view('admin.reports.trainers',compact('employee','trainers'));
    }

    public function duePaymentsReport(Request $request)
    {
        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $from = $request->from_date;
        $to = $request->end_date;

        if ($from && !$to) {
            $to = now()->toDateString();
        }

        if ($to && !$from) {
            $from = '1970-01-01';
        }

        if (!$from && !$to) {
            $from = now()->startOfMonth()->toDateString();
            $to = now()->endOfMonth()->toDateString();
        }

        $sales = User::with(['invoices' => fn ($q) => $q
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->withSum('payments', 'amount')])
            ->whereHas('invoices', function ($x) use ($to, $from, $branch_id) {
                $x->whereStatus('partial')
                    ->where('created_at', '>=', $from)
                    ->where('created_at', '<=', $to)
                    ->whereHas('membership')
                    ->when($branch_id, fn ($y) => $y->whereBranchId($branch_id));
            })
            ->withCount([
                'invoices' => fn ($q) => $q->whereStatus('partial')
                    ->where('created_at', '>=', $from)
                    ->where('created_at', '<=', $to)
                    ->when($branch_id, fn ($y) => $y->whereBranchId($branch_id))
            ])
            ->whereRelation('roles', 'title', 'Sales')
            ->get();


        $counter = 0;
        foreach ($sales as $sale) {
            $counter += $sale->invoices->sum('rest');
        }
        // $invoices = Invoice::with('sales_by')->whereStatus('partial')->withSum('payments', 'amount')->get()->groupBy('sales_by_id');
        return view('admin.reports.due_payments', compact('sales', 'counter', 'employee', 'branch_id'));
    }

    // public function showTrainerReport(Request $request, $trainer_id)
    // {
    //     $date = isset($request->date) ? $request->date : date('Y-m');

    //     $trainer = User::find($trainer_id);
    //     $memberships = Membership::withCount('attendances')
    //         ->with([
    //             'service_pricelist',
    //             'member',
    //             'invoice' => fn($q) => $q->withSum([
    //                 'payments' => fn($x) => $x->whereMonth('created_at', date('m', strtotime($date)))
    //                                     ->whereYear('created_at', date('Y', strtotime($date)))
    //             ],'amount')
    //         ])
    //         ->whereTrainerId($trainer_id)
    //         ->whereHas(
    //             'invoice',fn($q) => $q->where('status','!=','refund')
    //                 ->whereHas('payments',fn($x) => $x
    //                     ->whereMonth('created_at', date('m', strtotime($date)))
    //                     ->whereYear('created_at', date('Y', strtotime($date)))
    //                 )
    //         )
    //         ->get();

    //     $report = collect([]);
    //     $total = 0;
    //     $total_attendance = 0;
    //     $member_prefix = Setting::first() ? Setting::first()->member_prefix : NULL;
    //     $invoice_prefix = Setting::first() ? Setting::first()->invoice_prefix : NULL;

    //     foreach ($memberships as $membership) {
    //         $attendance_count = $membership->attendances_count;
    //         $report->push([
    //             'member' => [
    //                 'id'                    => $membership->member_id,
    //                 'member_code'           => $member_prefix . $membership->member->member_code,
    //                 'member_phone'          => $membership->member->phone,
    //                 'invoice_number'        => $invoice_prefix . $membership->invoice->id,
    //                 'invoice_id'            => $membership->invoice->id,
    //                 'membership_cost'       => $membership->invoice->payments->sum('amount'),
    //                 'name'                  => $membership->member->name,
    //                 'service'               => $membership->service_pricelist->name,
    //                 'attendance_count'      => $attendance_count,
    //                 'session_cost'          => $membership->invoice->payments->sum('amount') / ($membership->service_pricelist->session_count == 0 ? 1 : $membership->service_pricelist->session_count),
    //                 'sessions_total_cost'   => ($membership->invoice->payments->sum('amount') / ($membership->service_pricelist->session_count == 0 ? 1 : $membership->service_pricelist->session_count)) * $attendance_count
    //             ]
    //         ]);
    //         $total_attendance += $attendance_count;
    //         $total += ($membership->invoice->payments_sum_amount / ($membership->service_pricelist->session_count == 0 ? 1 : $membership->service_pricelist->session_count)) * $attendance_count;
    //     }
    //     $target_amount_percentage = isset($trainer->employee) && $trainer->employee->target_amount != NULL && $trainer->employee->target_amount > 0 ? ($total / $trainer->employee->target_amount) * 100 : 0;

    //     $db_sales_tier = SalesTier::whereHas('sales_tiers_users', fn ($q) => $q->where('user_id', $trainer->id))
    //         ->where('type', 'trainer')
    //         ->where('month', $date)
    //         ->first();
    //     if ($db_sales_tier) {
    //         $ranges = $db_sales_tier->sales_tiers_ranges()->where('range_to', '>', $target_amount_percentage)->where('range_from', '<=', $target_amount_percentage)->first();
    //     }

    //     $commission = [
    //         'sales_tier'            => isset($ranges) ? $ranges->sales_tier->name : trans('global.no_data_available'),
    //         'commission_value'      => isset($ranges) ? $ranges->commission : '0',
    //         'commission'            => isset($ranges) ? $total * ($ranges->commission / 100) : trans('global.no_data_available'),
    //         'sales_tier_month'      => isset($ranges) ? date('F', strtotime($ranges->sales_tier->month)) : ''
    //     ];

    //     $previous_memberships = Membership::whereHas(
    //                 'attendances', fn ($q) => $q->whereDate('created_at','<',date('Y-m-d', strtotime($date)))
    //         )->withCount('attendances')
    //         ->with([
    //             'service_pricelist',
    //             'member',
    //             'invoice' => fn($q) => $q->withSum([
    //                 'payments' => fn($x) => $x->whereMonth('created_at','<',date('m', strtotime($date)))
    //             ],'amount')
    //         ])
    //         ->whereTrainerId($trainer_id)
    //         ->whereHas(
    //             'invoice',fn($q) => $q->where('status','!=','refund')
    //                 ->whereHas('payments',fn($x) => $x
    //                     ->whereDate('created_at','<', date('Y-m-d',strtotime($date)))
    //                 )
    //         )
    //         ->get();

    //     $dates = [];

    //     $previous_month_report = collect([]);
    //     $pre_total = 0;
    //     $pre_total_attendance = 0;

    //     foreach ($previous_memberships as $pre_membership) {
    //         array_push($dates, date($date, strtotime($pre_membership->start_date)));
    //         $pre_attendance_count = $pre_membership->attendances_count;
    //         $previous_month_report->push([
    //             'member' => [
    //                 'id'                    => $pre_membership->member_id,
    //                 'member_code'           => $member_prefix . $pre_membership->member->member_code,
    //                 'member_phone'          => $pre_membership->member->phone,
    //                 'invoice_number'        => $invoice_prefix . $pre_membership->invoice->id,
    //                 'invoice_id'            => $pre_membership->invoice->id,
    //                 'membership_cost'       => $pre_membership->invoice->payments->sum('amount'),
    //                 'name'                  => $pre_membership->member->name,
    //                 'service'               => $pre_membership->service_pricelist->name,
    //                 'attendance_count'      => $pre_attendance_count,
    //                 'created_at'            => date($date, strtotime($pre_membership->start_date)),
    //                 'session_cost'          => $pre_membership->invoice->payments->sum('amount') / ($pre_membership->service_pricelist->session_count == 0 ? 1 : $pre_membership->service_pricelist->session_count),
    //                 'sessions_total_cost'   => ($pre_membership->invoice->payments->sum('amount') / ($pre_membership->service_pricelist->session_count == 0 ? 1 : $pre_membership->service_pricelist->session_count)) * $pre_attendance_count
    //             ]
    //         ]);
    //         $pre_total_attendance += $pre_attendance_count;
    //         $pre_total += ($pre_membership->invoice->payments_sum_amount / ($pre_membership->service_pricelist->session_count == 0 ? 1 : $pre_membership->service_pricelist->session_count)) * $pre_attendance_count;
    //     }
    //     $pre_sales_tier = SalesTier::whereHas('sales_tiers_users', function ($q) use ($trainer_id) {
    //         $q = $q->where('user_id', $trainer_id);
    //     })->whereIn('month', $dates)->get();
    //     $pre_commission = collect([]);
    //     $previous_months_commissions = 0;
    //     if ($pre_sales_tier->isNotEmpty()) {
    //         foreach ($pre_sales_tier as $pre_st) {
    //             foreach ($pre_st->sales_tiers_ranges()->where('range_to', '>', $total)->where('range_from', '<=', $total)->get() as $sales_tier_range) {
    //                 $previous_months_commissions += ($pre_total * ($sales_tier_range->commission / 100));
    //                 $pre_commission->push([
    //                     'sales_tier'         => $pre_st->name,
    //                     'commission_value'   => $sales_tier_range->commission,
    //                     'commission'         => $pre_total * ($sales_tier_range->commission / 100),
    //                     'sales_tier_month'   => date('F', strtotime($pre_st->month))
    //                 ]);
    //             }
    //         }
    //     }

    //     return view('admin.reports.showTrainer', [
    //         'trainer' => $trainer,
    //         'reports' => $report,
    //         'total' => $total,
    //         'commission' => $commission,
    //         'total_attendance' => $total_attendance,
    //         'previous_reports' => $previous_month_report,
    //         'pre_total' => $pre_total,
    //         'pre_commission' => $pre_commission,
    //         'pre_total_attendance' => $pre_total_attendance,
    //         'previous_months_commissions' => $previous_months_commissions
    //     ]);
    // }

    public function show_trainer_report(Request $request, $trainer_id)
    {
        $date = isset($request['date']) ? $request['date'] : date('Y-m');

        $trainer_service            = new TrainerService;
        $trainer                    = $trainer_service->trainer_show($request,$trainer_id);
        $trainer_service_payments   = $trainer_service->trainer_service_payments($date,$trainer_id);
        $trainer_payments           = $trainer_service->trainer_payments($date,$trainer_id);

        return view('admin.reports.show_trainer_new', [
            'trainer'                   => $trainer,
            'trainer_service_payments'  => $trainer_service_payments,
            'trainer_payments'          => $trainer_payments,
        ]);
    }

    public function dailyReport(Request $request)
    {

        $date = isset($request->date) ? $request->date : date('Y-m-d');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = isset($request['branch_id']) ? $request['branch_id'] : '';
        }

        $payments = Payment::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereHas('invoice', function ($q) {
            $q->whereHas('membership');
        })
            ->whereDate('created_at', $date)
            ->get();

        $external_payments = ExternalPayment::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->with(['lead', 'account', 'created_by'])
            ->whereDate('created_at', $date)
            ->get();

        $total_income = $payments->sum('amount') + $external_payments->sum('amount');

        //////////////////////////////////////////////
        $refunds = Refund::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->whereDate('created_at', $date)
            ->whereStatus('confirmed')
            ->get();

        $expenses = Expense::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->with(['expenses_category', 'account', 'created_by'])
            ->whereDate('date', $date)
            ->get();

        $loans = Loan::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->with(['employee', 'created_by'])
            ->whereDate('created_at', $date)
            ->get();

        $total_outcome = $refunds->sum('amount') + $expenses->sum('amount') + $loans->sum('amount');

        /////////////////////////////////////////////

        $net_income = $total_income - $total_outcome;

        ////////////////////////////////////////////

        $accounts = Account::whereManager(false)
            ->when($branch_id, fn ($x) => $x->whereBranchId($branch_id))
            ->with(['transactions' => fn ($q) => $q->whereDate('created_at', $date)])
            ->orderBy('name')
            ->get();

        ////////////////////////////////////////////


        // $services = Service::with([
        //                     'memberships' => fn($q) => $q->whereHas('invoice')->whereDate('memberships.created_at',$date),
        //                 ])->withCount([
        //                     'memberships' => fn($q) => $q->whereHas('invoice',fn($y) => $y->when($branch_id,fn($x) => $x->whereBranchId($branch_id)))->whereDate('memberships.created_at',$date)
        //                 ])->whereHas('memberships',function($q) use ($date,$request,$branch_id){
        //                     $q->whereHas('invoice',fn($y) => $y->when($branch_id,fn($x) => $x->whereBranchId($branch_id)))->whereDate('memberships.created_at','=',$date);
        //                 }
        //             )->get();

        ///////////////////////////////////////////
        // $allPayments = Payment::whereHas('account',function($q) use ($request,$branch_id){
        //                     $q->whereManager(false)->when($branch_id,fn($x) => $x->whereBranchId($branch_id));
        //                 })->whereHas('invoice',function($q){
        //                     $q->whereHas('membership');
        //                 })
        //                 ->whereDate('created_at',$date)
        //                 ->get();

        // $renewals_payments = 0;
        // $new_payments = 0;
        // $renewals_payments_count = 0;
        // $new_payments_count = 0;

        // foreach ($allPayments as $key => $payment)
        // {
        //     $status = $payment->invoice->membership->membership_status;
        //     if ($status == 'renew')
        //     {
        //         $renewals_payments += $payment->amount;
        //         $renewals_payments_count += 1;
        //     }else{
        //         $new_payments += $payment->amount;
        //         $new_payments_count += 1;
        //     }
        // }

        ////////////////////////////

        $service_payments = Payment::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereDate('created_at', $date)
            ->whereHas('invoice', function ($q) {
                $q->whereHas('membership', function ($x) {
                    $x->whereHas('service_pricelist', function ($t) {
                        $t->whereHas('service', function ($r) {
                            $r->whereHas('service_type');
                        });
                    });
                });
            })
            ->with(['invoice', 'invoice.membership', 'invoice.membership.member', 'invoice.membership.member.branch', 'invoice.membership.service_pricelist', 'invoice.membership.service_pricelist.service', 'invoice.membership.service_pricelist.service.service_type', 'account', 'created_by'])
            ->get()
            ->groupBy('invoice.membership.service_pricelist.service.service_type.name');

        $service_refunds = Refund::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereDate('created_at', $date)->whereStatus('confirmed')
            ->whereHas('invoice', function ($q) {
                $q->whereHas('membership', function ($x) {
                    $x->whereHas('service_pricelist', function ($t) {
                        $t->whereHas('service', function ($r) {
                            $r->whereHas('service_type');
                        });
                    });
                });
            })
            ->with(['invoice', 'invoice.membership', 'invoice.membership.member', 'invoice.membership.member.branch', 'invoice.membership.service_pricelist', 'invoice.membership.service_pricelist.service', 'invoice.membership.service_pricelist.service.service_type', 'account', 'created_by'])
            ->get()
            ->groupBy('invoice.membership.service_pricelist.service.service_type.name');


        // $selected_branch = Auth()->user()->employee->branch ?? NULL;

        return view('admin.reports.daily', compact(
            'total_income',
            'total_outcome',
            'net_income',
            'accounts',
            'payments',
            'external_payments',
            'refunds',
            'expenses',
            // 'services',
            // 'renewals_payments',
            // 'new_payments',
            // 'renewals_payments_count',
            // 'new_payments_count',
            'service_payments',
            'service_refunds',
            'loans',
            'branch_id',
            'employee',
        ));
    }

    public function printDailyReport(Request $request)
    {
        $date = isset(request()->date) ? request()->date : date('Y-m-d');

        $employee = Auth()->user()->employee;

        if ($employee) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = isset($request['branch_id']) ? $request['branch_id'] : '';
        }

        $payments = Payment::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereHas('invoice', function ($q) {
            $q->whereHas('membership');
        })
            ->with('invoice')
            ->whereDate('created_at', $date)
            ->get();

        $external_payments = ExternalPayment::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereDate('created_at', $date)
            ->get();

        $total_income = $payments->sum('amount') + $external_payments->sum('amount');

        //////////////////////////////////////////////

        $refunds = Refund::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereDate('created_at', $date)
            ->whereStatus('confirmed')
            ->get();

        $expenses = Expense::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereDate('date', $date)
            ->get();

        $loans = Loan::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereDate('created_at', $date)->get();

        $total_outcome = $refunds->sum('amount') + $expenses->sum('amount') + $loans->sum('amount');

        /////////////////////////////////////////////

        $net_income = $total_income - $total_outcome;

        ////////////////////////////////////////////

        $accounts = Account::whereManager(false)
            ->when($branch_id, fn ($x) => $x->whereBranchId($branch_id))
            ->with(['transactions' => fn ($q) => $q->whereDate('created_at', $date)])
            ->orderBy('name')
            ->get();

        ////////////////////////////////////////////


        $services = Service::with([
            'memberships' => fn ($q) => $q->whereHas('invoice')->whereDate('memberships.created_at', $date),
        ])->withCount([
            'memberships' => fn ($q) => $q->whereHas('invoice', fn ($y) => $y->when($branch_id, fn ($x) => $x->whereBranchId($branch_id)))->whereDate('memberships.created_at', $date)
        ])->whereHas(
            'memberships',
            function ($q) use ($date, $request, $branch_id) {
                $q->whereHas('invoice', fn ($y) => $y->when($branch_id, fn ($x) => $x->whereBranchId($branch_id)))->whereDate('memberships.created_at', '=', $date);
            }
        )->get();


        $allPayments = Payment::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereHas('invoice', function ($q) {
            $q->whereHas('membership');
        })
            ->whereDate('created_at', $date)
            ->get();

        $renewals_payments = 0;
        $new_payments = 0;
        $renewals_payments_count = 0;
        $new_payments_count = 0;

        foreach ($allPayments as $key => $payment) {
            $status = $payment->invoice->membership->membership_status;
            if ($status == 'renew') {
                $renewals_payments += $payment->amount;
                $renewals_payments_count += 1;
            } else {
                $new_payments += $payment->amount;
                $new_payments_count += 1;
            }
        }

        ////////////////////////////

        $service_payments = Payment::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereDate('created_at', $date)
            ->whereHas('invoice', function ($q) {
                $q->whereHas('membership', function ($x) {
                    $x->whereHas('service_pricelist', function ($t) {
                        $t->whereHas('service', function ($r) {
                            $r->whereHas('service_type');
                        });
                    });
                });
            })->get()->groupBy('invoice.membership.service_pricelist.service.service_type.name');

        $service_refunds = Refund::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereDate('created_at', $date)->whereStatus('confirmed')
            ->whereHas('invoice', function ($q) {
                $q->whereHas('membership', function ($x) {
                    $x->whereHas('service_pricelist', function ($t) {
                        $t->whereHas('service', function ($r) {
                            $r->whereHas('service_type');
                        });
                    });
                });
            })->get()->groupBy('invoice.membership.service_pricelist.service.service_type.name');

        $loans = Loan::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->whereDate('created_at', $date)
            ->get();

        $pdf = view('admin.reports.new_print', [
            'date'                      => $date,
            'total_income'              => $total_income,
            'total_outcome'             => $total_outcome,
            'net_income'                => $net_income,
            'accounts'                  => $accounts,
            'payments'                  => $payments,
            'external_payments'         => $external_payments,
            'refunds'                   => $refunds,
            'expenses'                  => $expenses,
            'services'                  => $services,
            'renewals_payments'         => $renewals_payments,
            'new_payments'              => $new_payments,
            'renewals_payments_count'   => $renewals_payments_count,
            'new_payments_count'        => $new_payments_count,
            'service_payments'          => $service_payments,
            'service_refunds'           => $service_refunds,
            'loans'                     => $loans,
        ]);
        PDF::SetTitle('Daily Report - ' . $date);
        PDF::AddPage('L', 'A4');
        PDF::SetFont('Dejavu Sans', '', 9);
        PDF::SetFont('Dejavu Sans', '', 9);
        PDF::writeHTML($pdf);
        PDF::Output('daily_report.pdf');
    }

    public function monthlyReport(Request $request)
    {

        $from = isset($request['from']) ? $request['from'] : date('Y-m-01');
        $to = isset($request['to']) ? $request['to'] : date('Y-m-t');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = isset($request['branch_id']) ? $request['branch_id'] : '';
        }

        $payments = Payment::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })->whereHas('invoice', function ($q) {
            $q->whereHas('membership');
        })
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->get();

        $external_payments = ExternalPayment::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->with(['lead', 'account', 'created_by'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->get();

        $total_income = $payments->sum('amount') + $external_payments->sum('amount');

        //////////////////////////////////////////////
        $refunds = Refund::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->whereStatus('confirmed')
            ->with(['invoice', 'invoice.membership', 'invoice.membership.member', 'invoice.membership.service_pricelist', 'account', 'created_by'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->get();

        $expenses = Expense::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->with(['expenses_category', 'account', 'created_by'])
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->get();

        $loans = Loan::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->with(['employee', 'created_by'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->get();

        $total_outcome = $refunds->sum('amount') + $expenses->sum('amount') + $loans->sum('amount');

        /////////////////////////////////////////////

        $net_income = $total_income - $total_outcome;

        ////////////////////////////////////////////

        $accounts = Account::whereManager(false)
            ->when($branch_id, fn ($x) => $x->whereBranchId($branch_id))
            ->with(['transactions' => fn ($q) => $q->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)])
            ->orderBy('name')
            ->get();

        ////////////////////////////////////////////

        // $services = Service::with([
        //     'memberships' => fn($q) => $q->whereHas('invoice')->whereBetween('memberships.created_at',[$from,$to]),
        //     ])->withCount([
        //         'memberships' => fn($q) => $q
        //                     ->whereHas('invoice',fn($y) => $y->when($branch_id,fn($x) => $x->whereBranchId($branch_id)))
        //                     ->whereBetween('memberships.created_at',[$from,$to])
        //     ])->whereHas('memberships',function($q) use ($from,$to,$request,$branch_id){
        //                 $q->whereHas('invoice',fn($y) => $y->when($branch_id,fn($x) => $x->whereBranchId($branch_id)))
        //                 ->whereBetween('memberships.created_at',[$from,$to]);
        //     }
        // )->get();

        ///////////////////////////////////////////
        // $allPayments = Payment::whereHas('account',function($q) use ($request,$branch_id){
        //                     $q->whereManager(false)->when($branch_id,fn($x) => $x->whereBranchId($branch_id));
        //                 })->whereHas('invoice',function($q){
        //                     $q->whereHas('membership');
        //                 })
        //             ->whereBetween('created_at',[$from,$to])
        //             ->get();

        // $renewals_payments = 0;
        // $new_payments = 0;
        // $renewals_payments_count = 0;
        // $new_payments_count = 0;

        // foreach ($allPayments as $key => $payment)
        // {
        //     $status = $payment->invoice->membership->membership_status;
        //     if ($status == 'renew')
        //     {
        //         $renewals_payments += $payment->amount;
        //         $renewals_payments_count += 1;
        //     }else{
        //         $new_payments += $payment->amount;
        //         $new_payments_count += 1;
        //     }
        // }

        $service_payments = Payment::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->where('created_at', '>=', $from)->where('created_at', '<=', $to)
            ->whereHas('invoice', function ($q) {
                $q->whereHas('membership', function ($x) {
                    $x->whereHas('service_pricelist', function ($t) {
                        $t->whereHas('service', function ($r) {
                            $r->whereHas('service_type');
                        });
                    });
                });
            })
            ->with(['invoice', 'invoice.membership', 'invoice.membership.member', 'invoice.membership.member.branch', 'invoice.membership.service_pricelist', 'invoice.membership.service_pricelist.service', 'invoice.membership.service_pricelist.service.service_type', 'account', 'created_by'])
            ->get()
            ->groupBy('invoice.membership.service_pricelist.service.service_type.name');

        $service_refunds = Refund::whereHas('account', function ($q) use ($request, $branch_id) {
            $q->whereManager(false)->when($branch_id, fn ($x) => $x->whereBranchId($branch_id));
        })
            ->where('created_at', '>=', $from)->where('created_at', '<=', $to)
            ->whereHas('invoice', function ($q) {
                $q->whereHas('membership', function ($x) {
                    $x->whereHas('service_pricelist', function ($t) {
                        $t->whereHas('service', function ($r) {
                            $r->whereHas('service_type');
                        });
                    });
                });
            })
            ->with(['invoice', 'invoice.membership', 'invoice.membership.member', 'invoice.membership.member.branch', 'invoice.membership.service_pricelist', 'invoice.membership.service_pricelist.service', 'invoice.membership.service_pricelist.service.service_type', 'account', 'created_by'])
            ->get()
            ->groupBy('invoice.membership.service_pricelist.service.service_type.name');


        return view('admin.reports.monthly', compact(
            'total_income',
            'total_outcome',
            'net_income',
            'accounts',
            'payments',
            'external_payments',
            'refunds',
            'expenses',
            // 'services',
            // 'renewals_payments',
            // 'renewals_payments_count',
            // 'new_payments',
            // 'new_payments_count',
            'service_payments',
            'service_refunds',
            'loans',
            'branch_id',
            'employee',
        ));
    }

    public function printMonthlyReport(Request $request)
    {

        $date = isset($request->date) ? $request->date : date('Y-m');

        $payments = Payment::whereHas('invoice', function ($q) {
            $q = $q->where('status', '!=', 'refund');
        })->with('invoice')->whereYear('created_at', date('Y', strtotime($date)))->whereMonth('created_at', date('m', strtotime($date)))->get();

        $external_payments = ExternalPayment::whereYear('created_at', date('Y', strtotime($date)))->whereMonth('created_at', date('m', strtotime($date)))->get();

        $total_income = $payments->sum('amount') + $external_payments->sum('amount');

        //////////////////////////////////////////////
        $refunds = Refund::whereStatus('confirmed')->whereYear('created_at', date('Y', strtotime($date)))->whereMonth('created_at', date('m', strtotime($date)))->get();

        $expenses = Expense::whereYear('created_at', date('Y', strtotime($date)))->whereMonth('created_at', date('m', strtotime($date)))->get();

        $total_outcome = $refunds->sum('amount') + $expenses->sum('amount');

        /////////////////////////////////////////////

        $net_income = $total_income - $total_outcome;

        ////////////////////////////////////////////

        $accounts = Account::with(['transactions' => fn ($q) => $q->whereYear('created_at', date('Y', strtotime($date)))->whereMonth('created_at', date('m', strtotime($date)))])->latest()->get();

        ////////////////////////////////////////////

        $services = Service::with([
            'memberships' => fn ($q) => $q->whereHas('invoice')
                ->whereYear('memberships.created_at', date('Y', strtotime($date)))
                ->whereMonth('memberships.created_at', date('m', strtotime($date))),
        ])->withCount([
            'memberships' => fn ($q) => $q
                ->whereHas('invoice')
                ->whereYear('memberships.created_at', date('Y', strtotime($date)))
                ->whereMonth('memberships.created_at', date('m', strtotime($date)))
        ])->whereHas(
            'memberships',
            function ($q) use ($date) {
                $q->whereHas('invoice')
                    ->whereYear('memberships.created_at', date('Y', strtotime($date)))
                    ->whereMonth('memberships.created_at', date('m', strtotime($date)));
            }
        )->get();

        ///////////////////////////////////////////

        $allPayments = Payment::whereHas('invoice', function ($q) {
            $q->whereHas('membership');
        })
            ->whereYear('created_at', date('Y', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->get();

        $renewals_payments = 0;
        $new_payments = 0;
        $renewals_payments_count = 0;
        $new_payments_count = 0;

        foreach ($allPayments as $key => $payment) {
            $status = $payment->invoice->membership->membership_status;
            if ($status == 'renew') {
                $renewals_payments += $payment->amount;
                $renewals_payments_count += 1;
            } else {
                $new_payments += $payment->amount;
                $new_payments_count += 1;
            }
        }

        $service_payments = Payment::whereYear('created_at', date('Y', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->whereHas('invoice', function ($q) {
                $q->whereHas('membership', function ($x) {
                    $x->whereHas('service_pricelist', function ($t) {
                        $t->whereHas('service', function ($r) {
                            $r->whereHas('service_type');
                        });
                    });
                });
            })
            ->get()
            ->groupBy('invoice.membership.service_pricelist.service.service_type.name');

        $service_refunds = Refund::whereYear('created_at', date('Y', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->whereHas('invoice', function ($q) {
                $q->whereHas('membership', function ($x) {
                    $x->whereHas('service_pricelist', function ($t) {
                        $t->whereHas('service', function ($r) {
                            $r->whereHas('service_type');
                        });
                    });
                });
            })
            ->get()
            ->groupBy('invoice.membership.service_pricelist.service.service_type.name');

        $loans = Loan::whereYear('created_at', date('Y', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->get();

        $pdf = view('admin.reports.new_print', [
            'date' => $date,
            'total_income' => $total_income,
            'total_outcome' => $total_outcome,
            'net_income' => $net_income,
            'accounts' => $accounts,
            'payments' => $payments,
            'external_payments' => $external_payments,
            'refunds' => $refunds,
            'expenses' => $expenses,
            'services' => $services,
            'renewals_payments' => $renewals_payments,
            'renewals_payments_count' => $renewals_payments_count,
            'new_payments' => $new_payments,
            'new_payments_count' => $new_payments_count,
            'service_payments' => $service_payments,
            'service_refunds' => $service_refunds,
            'loans' => $loans,
        ]);
        PDF::SetTitle('Monthly Report - ' . $date);
        PDF::AddPage('L', 'A4');
        PDF::SetFont('Dejavu Sans', '', 9);
        PDF::writeHTML($pdf);
        PDF::Output('monthly_report.pdf');
    }

    public function yearlyFinanceReport(Request $request)
    {
        $months = [];

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        if (!isset($request->year)) {
            $year = date('Y');
        } else {
            $year = $request->year;
        }

        for ($month = 1; $month <= 12; $month++) {
            $payments = Payment::whereHas('invoice', function ($q) use ($branch_id) {
                $q = $q->where('status', '!=', 'refund');
            })
                ->with('invoice')
                ->whereYear('created_at', date($year))
                ->whereMonth('created_at', date($month))
                ->whereHas('account', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
                ->get();

            $external_payments = ExternalPayment::whereYear('created_at', date($year))
                ->whereMonth('created_at', date($month))
                ->whereHas('account', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
                ->get();

            $months[$month]['total_income'] = $payments->sum('amount') + $external_payments->sum('amount');

            $refunds = Refund::whereStatus('confirmed')
                ->whereYear('created_at', date($year))
                ->whereMonth('created_at', date($month))
                ->whereHas('invoice', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
                ->get();

            $expenses = Expense::whereYear('created_at', date($year))
                ->whereMonth('created_at', date($month))
                ->whereHas('account', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
                ->get();

            $months[$month]['total_outcome'] = $refunds->sum('amount') + $expenses->sum('amount');

            $months[$month]['net_income'] = $months[$month]['total_income'] - $months[$month]['total_outcome'];
        }


        return view('admin.reports.yearly_finance', compact('months', 'employee', 'branch_id'));
    }

    public function monthlyFinanceReport(Request $request)
    {
        // return $request->month;
        $month = isset($request->month) ? $request->month : date('Y-m');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $lastDay = date('t');

        $data = [];

        for ($i = 1; $i <= $lastDay; $i++) {
            $day = date($month) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);

            $payments = Payment::whereHas('invoice', function ($q) use ($branch_id) {
                $q = $q->where('status', '!=', 'refund');
            })
                ->with('invoice')
                ->whereYear('created_at', date('Y', strtotime($month)))
                ->whereDate('created_at', date($day))
                ->whereHas('account', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
                ->get();

            $external_payments = ExternalPayment::whereYear('created_at', date('Y', strtotime($month)))
                ->whereDate('created_at', date($day))
                ->whereHas('account', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
                ->get();

            $data[$i]['total_income'] = $payments->sum('amount') + $external_payments->sum('amount');

            $refunds = Refund::whereStatus('confirmed')
                ->whereYear('created_at', date('Y', strtotime($month)))
                ->whereDate('created_at', date($day))
                ->whereHas('invoice', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
                ->get();

            $expenses = Expense::whereYear('created_at', date('Y', strtotime($month)))
                ->whereDate('created_at', date($day))
                ->whereHas('account', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
                ->get();

            $data[$i]['total_outcome'] = $refunds->sum('amount') + $expenses->sum('amount');

            $data[$i]['net_income'] = $data[$i]['total_income'] - $data[$i]['total_outcome'];
        }

        // dd($data);

        return view('admin.reports.monthly_finance', compact('data', 'employee', 'branch_id'));
    }

    public function expensesReport(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $expensesCategory = ExpensesCategory::whereHas('expenses', function ($i) use ($date) {
            $i->whereYear('created_at', date('Y', strtotime($date)))
                ->whereMonth('created_at', date('m', strtotime($date)));
        })->withCount([
            'expenses' => fn ($q) => $q
                ->whereYear('created_at', date('Y', strtotime($date)))
                ->whereMonth('created_at', date('m', strtotime($date)))
        ])->get();

        return view('admin.reports.expenses', compact('expensesCategory'));
    }

    public function refundReasonsReport(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $refundReasons = RefundReason::whereHas('refunds')
            ->with([
                'refunds' => fn ($x) => $x
                    ->whereStatus('confirmed')
                    ->whereYear('created_at', date('Y', strtotime($date)))
                    ->whereMonth('created_at', date('m', strtotime($date)))
            ])
            ->whereHas('refunds', function ($q) use ($date, $branch_id) {
                $q->whereStatus('confirmed')
                    ->whereYear('created_at', date('Y', strtotime($date)))
                    ->whereMonth('created_at', date('m', strtotime($date)))
                    ->whereHas('invoice', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)));
            })
            ->withCount([
                'refunds' => fn ($i) =>
                $i->whereStatus('confirmed')
                    ->whereYear('created_at', date('Y', strtotime($date)))
                    ->whereMonth('created_at', date('m', strtotime($date)))
                    ->whereHas('invoice', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
            ])
            ->get();

        $refunds = Refund::whereStatus('confirmed')
            ->whereYear('created_at', date('Y', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->whereHas('invoice', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
            ->sum('amount');

        return view('admin.reports.refundReasons', compact('refundReasons', 'refunds', 'employee', 'branch_id'));
    }

    public function removeCache()
    {
        cache()->flush();
        return back();
    }


    public function sessionsAttendancesReport(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m-d');
        $sessions = SessionList::whereHas('schedules', fn ($q) => $q->where('day', date('D', strtotime($date))))->with('schedules')->get();
        $data = collect([]);
        foreach ($sessions as $session) {
            $attendance_count = 0;
            foreach ($session->trainer_attendants as $attendance) {
                if ($attendance->created_at->format('Y-m-d') == $date) {
                    $attendance_count += 1;
                }
            }
            $schedules =  $session->schedules()->where('day', date('D', strtotime($date)))->get();

            // $timeslots = Timeslot::orderBy('from','asc')->get();
            // dd($timeslots);
            foreach ($schedules as $schedule) {
                $data->push([
                    'session'           => $session->name,
                    'color'             => $session->color,
                    'timeslot'          => date('g:i A', strtotime($schedule->timeslot->from)) . ' ' . trans('global.to') . ' ' . date('g:i A', strtotime($schedule->timeslot->to)),
                    'schedule_trainer'  => $schedule->trainer->name,
                    'attendance_count'  => $attendance_count,
                    'attendants'        => $schedule->trainer_attendants()->whereDate('created_at', $date)->get()
                ]);
            }
        }
        return view('admin.reports.sessions_attendances')->with([
            'data'              => $data
        ]);
    }


    public function exportSessionsAttendancesReport(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m-d');

        $sessions = SessionList::whereHas('schedules', fn ($q) => $q->where('day', date('D', strtotime($date))))->with('schedules')->get();

        $data = collect([]);
        foreach ($sessions as $session) {
            $attendance_count = 0;
            foreach ($session->trainer_attendants as $attendance) {
                if ($attendance->created_at->format('Y-m-d') == $date) {
                    $attendance_count += 1;
                }
            }
            foreach ($session->schedules()->where('day', date('D', strtotime($date)))->get() as $schedule) {
                $data->push([
                    'session'           => $session->name,
                    'color'             => $session->color,
                    'timeslot'          => date('g:i A', strtotime($schedule->timeslot->from)) . ' ' . trans('global.to') . ' ' . date('g:i A', strtotime($schedule->timeslot->to)),
                    'schedule_trainer'  => $schedule->trainer->name,
                    'attendance_count'  => $attendance_count,
                    'attendants'        => $schedule->trainer_attendants()->whereDate('created_at', $date)->get()->map(function ($queries) {
                        return [
                            'member_name'   => $queries->member->name,
                            'member_code'   => $queries->member->member_code,
                            'member_phone'  => $queries->member->phone,
                            'gender'        => Str::ucfirst($queries->member->gender)
                        ];
                    })
                ]);
            }
        }

        return Excel::download(new ExportSessionAttendancesReport($data), 'session-attendances.xlsx');
    }

    public function reminders(Request $request)
    {
        $employee = Auth()->user()->employee;
        $type = Auth()->user()->roles[0]->title;
        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }
        $sales = User::whereRelation('roles', 'title', 'Sales')
            ->whereHas('employee', fn ($q) => $q->whereStatus('active')->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
            ->orderBy('name')
            ->whereHas('reminders')
            ->with(['reminders', 'todayReminders', 'upcommingReminders', 'overdueReminders'])

            ->withCount(['reminders', 'todayReminders', 'upcommingReminders', 'overdueReminders']);

        if($type == 'Sales'){
            $sales = $sales->where('id',Auth()->user()->id);
        }
        $sales=$sales->get();
        return view('admin.reports.reminders', compact('sales', 'employee', 'branch_id'));
    }

    public function monthlyReportExport(Request $request)
    {
        return Excel::download(new MonthlyReportExport($request->all()), 'Monthly-Report-' . $request['date'] . '.xlsx');
    }

    public function sessionsInstructed(Request $request)
    {
        $sessions = TrainerAttendant::when($request->input('trainer'), fn ($query) => $query->where('trainer_id', User::findOrFail($request->input('trainer'))->id))
            ->when($request->input('session'), function ($query) use ($request) {
                $query->whereIn('schedule_id', SessionList::find($request->input('session'))->schedules->pluck('id')->toArray());
            })
            ->whereMonth('created_at', explode('-', $request->input('date'))[1])
            ->whereYear('created_at', explode('-', $request->input('date'))[0])
            ->get([
                'trainer_id',
                'schedule_id',
                DB::raw('DATE(created_at) AS day')
            ])->unique('day');

        return view('admin.reports.session-instructed', ['sessions' => $sessions]);
    }

    public function athletesInstructed(Request $request)
    {
        $athletes = TrainerAttendant::when($request->input('trainer'), fn ($query) => $query->where('trainer_id', User::findOrFail($request->input('trainer'))->id))
            ->when($request->input('session'), function ($query) use ($request) {
                $query->whereIn('schedule_id', SessionList::find($request->input('session'))->schedules->pluck('id')->toArray());
            })
            ->with(['schedule', 'schedule.timeslot', 'trainer', 'member'])
            ->whereMonth('created_at', explode('-', $request->input('date'))[1])
            ->whereYear('created_at', explode('-', $request->input('date'))[0])
            ->get();

        return view('admin.reports.athletes-instructed', ['athletes' => $athletes->groupBy('member_id'), 'counter' => $athletes->count()]);
    }


    public function revenueDetails(Request $request)
    {
        $attendants = TrainerAttendant::query()
            ->when($request->input('trainer'), fn ($query) => $query->where('trainer_id', User::findOrFail($request->input('trainer'))->id))
            ->when($request->input('session'), function ($query) use ($request) {
                $query->whereIn('schedule_id', SessionList::find($request->input('session'))->schedules->pluck('id')->toArray());
            })
            ->with(['schedule', 'schedule.timeslot', 'trainer', 'member', 'membership', 'membership.service_pricelist', 'membership.invoice'])
            ->whereMonth('created_at', explode('-', $request->input('date'))[1])
            ->whereYear('created_at', explode('-', $request->input('date'))[0]);
        $counter = 0;
        foreach ($attendants->get() as $att) {
            $counter += ($att->membership->invoice->net_amount / $att->membership->service_pricelist->session_count);
        }
        return view('admin.reports.revenue-details', ['attendants' => $attendants->get(), 'revenue' => round($counter)]);
    }

    public function exportRevenueDetailsReport(Request $request)
    {
        $attendants = TrainerAttendant::query()
            ->when($request->input('trainer'), fn ($query) => $query->where('trainer_id', User::findOrFail($request->input('trainer'))->id))
            ->when($request->input('session'), function ($query) use ($request) {
                $query->whereIn('schedule_id', SessionList::find($request->input('session'))->schedules->pluck('id')->toArray());
            })
            ->with(['schedule', 'schedule.timeslot', 'trainer', 'member', 'membership', 'membership.service_pricelist', 'membership.invoice'])
            ->whereMonth('created_at', explode('-', $request->input('date'))[1])
            ->whereYear('created_at', explode('-', $request->input('date'))[0])
            ->get();
        return Excel::download(new ExportRevenueDetailsReport($attendants), 'revenue-details-report.xlsx');
    }

    public function exportSessionsInstructedReport(Request $request)
    {
        $sessions = TrainerAttendant::when($request->input('trainer'), fn ($query) => $query->where('trainer_id', User::findOrFail($request->input('trainer'))->id))
            ->when($request->input('session'), function ($query) use ($request) {
                $query->whereIn('schedule_id', SessionList::find($request->input('session'))->schedules->pluck('id')->toArray());
            })
            ->whereMonth('created_at', explode('-', $request->input('date'))[1])
            ->whereYear('created_at', explode('-', $request->input('date'))[0])
            ->get([
                'trainer_id',
                'schedule_id',
                DB::raw('DATE(created_at) AS day')
            ])->unique('day');
        return Excel::download(new ExportSessionsInstructedReport($sessions), 'sessions-instructed-report.xlsx');
    }

    public function exportAthletesInstructedReport(Request $request)
    {
        $athletes = TrainerAttendant::when($request->input('trainer'), fn ($query) => $query->where('trainer_id', User::findOrFail($request->input('trainer'))->id))
            ->when($request->input('session'), function ($query) use ($request) {
                $query->whereIn('schedule_id', SessionList::find($request->input('session'))->schedules->pluck('id')->toArray());
            })
            ->with(['schedule', 'schedule.timeslot', 'trainer', 'member'])
            ->whereMonth('created_at', explode('-', $request->input('date'))[1])
            ->whereYear('created_at', explode('-', $request->input('date'))[0])
            ->get();

        return Excel::download(new ExportAthletesInstructedReport($athletes->groupBy('member_id')), 'athletes-instructed-report.xlsx');
    }


    public function exportCoachesReport(Request $request)
    {
        $trainers = User::whereRelation('roles', 'title', 'Trainer')->whereHas('sessions')->get();
        $reports = collect([]);
        foreach ($trainers as $trainer) {
            $trainer_attendants = TrainerAttendant::query()
                ->with(['schedule', 'schedule.session', 'schedule.session.service', 'schedule.session.service.service_pricelist', 'membership', 'membership.service_pricelist', 'membership.invoice'])
                ->where('trainer_id', $trainer->id)
                ->when($request->input('month'), function ($query) use ($request) {
                    $query->whereMonth('created_at', explode('-', $request->month)[1])
                        ->whereYear('created_at', explode('-', $request->month)[0]);
                })
                ->unless($request->input('month'), function ($query) {
                    $query->whereMonth('created_at', date('m'))
                        ->whereYear('created_at', date('Y'));
                });
            $sessions_instructed = $trainer_attendants->get([
                'trainer_id',
                'schedule_id',
                DB::raw('DATE(created_at) AS day')
            ])->unique('day')->count();
            $athletes_instructed = $trainer_attendants->get()->unique('member_id')->count();
            $revenue = 0;
            foreach ($trainer_attendants->get() as $attend) {
                if ($attend->membership != NULL && $attend->membership->service_pricelist->session_count != 0) {
                    $revenue += ($attend->membership->invoice->net_amount /
                        $attend->membership->service_pricelist->session_count);
                }
            }
            if ($sessions_instructed > 0) {
                $reports->push([
                    'trainer_name'                  => $trainer->name,
                    'trainer_id'                    => $trainer->id,
                    'sessions_instructed'           => $sessions_instructed,
                    'athletes_instructed'           => $athletes_instructed,
                    'revenue'                       => round($revenue)
                ]);
            }
        }

        return Excel::download(new ExportCoachesReport($reports), 'coaches-report.xlsx');
    }

    public function exportRevenueReport(Request $request)
    {
        $sessions_query = SessionList::query()->whereHas('trainer_attendants')->with('service');
        $session_ids = $sessions_query->pluck('id')->toArray();
        $sessions = $sessions_query->get();
        $schedules = Schedule::whereIn('session_id', $session_ids)->pluck('id')->toArray();
        $trainer_attendants = TrainerAttendant::with(['schedule', 'membership', 'membership.invoice', 'membership.service_pricelist'])->whereIn('schedule_id', $schedules)
            ->when($request->input('month'), function ($query) use ($request) {
                $query->whereMonth('created_at', explode('-', $request->input('month'))[1])
                    ->whereYear('created_at', explode('-', $request->input('month'))[0]);
            })
            ->unless($request->input('month'), fn ($q) => $q->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y')))
            ->get();
        $report = [];
        foreach ($sessions as $sess) {
            $report[$sess->id] = [
                'session_id'        => $sess->id,
                'attendants'        => 0,
                'sessions_count'    => 0,
                'revenue'           => 0,
                'utilization_rate'  => $sess->ranking,
                'session'           => [
                    'name'          => $sess->name,
                    'color'         => $sess->color,
                    'max_capacity'  => $sess->max_capacity,
                    'ranking'       => $sess->ranking
                ]
            ];
        }
        foreach ($trainer_attendants as $trainer_attendant) {
            $report[$trainer_attendant->schedule->session_id]['attendants'] += 1;
            $report[$trainer_attendant->schedule->session_id]['revenue'] += $trainer_attendant->membership->invoice->net_amount / $trainer_attendant->membership->service_pricelist->session_count;
        }
        foreach ($sessions as $session) {

            $report[$session->id]['sessions_count'] = TrainerAttendant::whereIn('schedule_id', $session->schedules()->pluck('id')->toArray())
                ->when($request->input('month'), function ($query) use ($request) {
                    $query->whereMonth('created_at', explode('-', $request->input('month'))[1])
                        ->whereYear('created_at', explode('-', $request->input('month'))[0]);
                })
                ->unless($request->input('month'), fn ($q) => $q->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y')))->distinct(DB::raw('DATE(created_at)'))->count();


            if ($report[$session->id]['attendants'] > 0) {
                $session->append(['ranking' => round(($report[$session->id]['attendants'] / ($report[$session->id]['sessions_count'] * $report[$session->id]['session']['max_capacity'])) * 100)]);
            }

            $report[$session->id]['utilization_rate'] = $report[$session->id]['attendants'] > 0 ? round(($report[$session->id]['attendants'] / ($report[$session->id]['sessions_count'] * $report[$session->id]['session']['max_capacity'])) * 100, 2) : 0;
        }

        return Excel::download(new ExportRevenuesReport($report), 'revenues-report.xlsx');
    }

    public function exportTrainersReport(Request $request)
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

            foreach ($memberships as $membership) {
                $attendance_count = $membership->attendances_count;
                $total_attendance += $attendance_count;
                $total += ($membership->invoice->payments->sum('amount') / ($membership->service_pricelist->session_count == 0 ? 1 : $membership->service_pricelist->session_count)) * $attendance_count;
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
                'total'                 => $total
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

        return Excel::download(new ExportTrainersReport($trainers, $commission, $pre_commission), 'trainers-report.xlsx');
    }

    public function freezeRequestsExport(Request $request)
    {
        $report = FreezeRequest::index($request->except('_token'))->whereIn('status', ['pending', 'confirmed', 'rejected'])->with(['membership', 'created_by', 'membership.service_pricelist', 'membership.member'])->get();
        return Excel::download(new FreezeRequestsExport($report), 'freeze-requests.xlsx');
    }

    public function expiredMembershipAttendanceReport(Request $request)
    {
        $membershipAttendances = MembershipAttendance::index($request->except('page'))
            ->whereMembershipStatus('expired')
            ->paginate(10);

        return view('admin.reports.expired-membership-attendances', ['membershipAttendances' => $membershipAttendances]);
    }

    public function exportMembershipAttendancesReport(Request $request)
    {
        return Excel::download(new ExportExpiredMembershipAttendancesReport(), 'expired-membership-attendances.xlsx');
    }

    public function currentMembershipsReport(Request $request)
    {
        $memberships = Membership::index($request->except('page'))
            ->with(['member', 'trainer', 'service_pricelist', 'service_pricelist.service'])
            ->whereHas('service_pricelist.service.service_type', fn ($q) => $q->where('main_service', 1))
            ->where('status', 'current')
            ->orderBy('member_id', 'ASC')
            ->paginate(10);
        $services = Service::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $sales = User::whereRelation('roles', 'title', 'sales')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.reports.current-memberships', compact('memberships', 'services', 'sales'));
    }

    public function exportCurrentMembershipsReport(Request $request)
    {
        $memberships = Membership::index($request->all())
            ->whereHas('service_pricelist.service.service_type', fn ($q) => $q->where('main_service', 1))
            ->whereStatus('current')
            ->get();

        return Excel::download(new ExportCurrentMembershipsReport($memberships), 'current-memberships.xlsx');
    }

    public function trainerCommissions(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $trainers = User::whereRelation('roles', 'title', 'Trainer')->get();
        $sessions_array = [];
        foreach ($trainers as $trainer) {
            $attendances = TrainerAttendant::where('trainer_id', $trainer->id)
                // ->whereDate('created_at','>=','2022-06-01')
                // ->where('created_at','<=','2022-06-30')
                ->whereYear('created_at', date('Y', strtotime($date)))
                ->whereMonth('created_at', date('m', strtotime($date)))
                ->get();

            foreach ($attendances as $attend) {
                $sessions_array[$trainer->id][$attend->schedule->id][date('Y-m-d', strtotime($attend->created_at))][] =  $attend->membership->id;
            }
        }
        return view('admin.reports.trainer_commissions', compact('trainers', 'sessions_array'));
    }

    public function showTrainerCommissions(Request $request, $id)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $trainer = User::findOrFail($id);
        $sessions_array = [];
        $attendances = TrainerAttendant::where('trainer_id', $id)
            // ->where('created_at','>=','2022-06-01')
            // ->where('created_at','<=','2022-06-30')
            ->whereYear('created_at', date('Y', strtotime($date)))
            ->whereMonth('created_at', date('m', strtotime($date)))
            ->get();

        foreach ($attendances as $attend) {
            $sessions_array[$attend->schedule->id][date('Y-m-d', strtotime($attend->created_at))][] =  $attend->membership->id;
        }

        return view('admin.reports.show_trainer_commissions', compact('trainer', 'sessions_array'));
    }

    public function showSessionAttendances($trainer_id, $schedule_id, $session_date)
    {
        $attendances = TrainerAttendant::where('trainer_id', $trainer_id)
            ->where('schedule_id', $schedule_id)
            ->whereDate('created_at', $session_date)
            ->get();
        $trainer = User::find($trainer_id);
        $schedule = Schedule::find($schedule_id);

        return view('admin.reports.showSessionAttendances', compact('attendances', 'trainer', 'schedule', 'session_date'));
    }

    public function remindersAction(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m-d');

        $type = Auth()->user()->roles[0]->title;
        $employee = Auth()->user()->employee;
        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }
        $sales = User::whereRelation('roles', 'title', 'Sales')
            ->with([
                'reminders'             => fn ($q) => $q->whereDate('due_date', $date),
                'reminders_histories'   => fn ($q) => $q->whereDate('due_date', $date),
            ])
            ->whereHas('reminders', function ($q) use ($date) {
                $q->whereDate('due_date', $date);
            })
            ->withCount([
                'reminders'             => fn ($q) => $q->whereDate('due_date', $date),
                'reminders_histories'   => fn ($q) => $q->whereDate('due_date', $date),
            ])->whereHas('employee', fn ($q) => $q->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
            ->whereHas('employee',fn($q) => $q->whereStatus('active'));
        if($type == 'Sales'){
            $sales = $sales->where('id',Auth()->user()->id);
        }
        $sales = $sales->get();

        return view('admin.reports.reminders_action_history', compact('sales'));
    }

    public function externalPaymentCategories(Request $request)
    {
        $date = isset($request->date) ? $request->date : date('Y-m');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $external_payment_categories = ExternalPaymentCategory::withSum([
            'external_payments' => fn ($q) => $q
                ->whereYear('created_at', date('Y-m', strtotime($date)))
                ->whereMonth('created_at', date('m', strtotime($date)))
                ->whereHas('account', fn ($y) => $y->when($branch_id, fn ($x) => $x->whereBranchId($branch_id)))
        ], 'amount')
            ->whereHas(
                'external_payments',
                fn ($q) => $q
                    ->whereYear('created_at', date('Y-m', strtotime($date)))
                    ->whereMonth('created_at', date('m', strtotime($date)))
                    ->whereHas('account', fn ($y) => $y->when($branch_id, fn ($x) => $x->whereBranchId($branch_id)))
            )
            ->withCount([
                'external_payments' => fn ($q) => $q
                    ->whereYear('created_at', date('Y-m', strtotime($date)))
                    ->whereMonth('created_at', date('m', strtotime($date)))
                    ->whereHas('account', fn ($y) => $y->when($branch_id, fn ($x) => $x->whereBranchId($branch_id)))
            ])
            ->get();

        return view('admin.reports.external_payment_categories', compact('external_payment_categories', 'branch_id', 'employee'));
    }

    public function sales_details(Request $request, $id)
    {
        $date = $request['date'] != NULL ? $request['date'] : date('Y-m');

        $branch = Branch::findOrFail($id);

        $sources = Source::with([
            'leads'   => fn ($q) => $q->whereBranchId($branch->id)->whereYear('created_at', date('Y', strtotime($date)))->whereMonth('created_at', date('m', strtotime($date))),

            'members' => fn ($q) => $q->whereBranchId($branch->id)->whereYear('created_at', date('Y', strtotime($date)))->whereMonth('created_at', date('m', strtotime($date)))->whereHas('invoices', fn ($y) => $y->withSum('payments', 'amount'))
        ])
            ->withCount([
                'leads'   => fn ($q) => $q->whereBranchId($branch->id)->whereYear('created_at', date('Y', strtotime($date)))->whereMonth('created_at', date('m', strtotime($date))),

                'members' => fn ($q) => $q->whereBranchId($branch->id)->whereYear('created_at', date('Y', strtotime($date)))->whereMonth('created_at', date('m', strtotime($date)))->whereHas('invoices')
            ])
            ->get();

        $sales = User::with(['invoices' => fn ($q) => $q->withSum('payments', 'amount')])
            ->whereHas('invoices', function ($x) use ($branch) {
                $x->whereStatus('partial')
                    ->whereHas('membership')
                    ->whereBranchId($branch->id);
            })
            ->withCount([
                'invoices' => fn ($q) => $q->whereStatus('partial')
                    ->whereBranchId($branch->id)
            ])
            ->whereRelation('roles', 'title', 'Sales')
            ->get();

        return view('admin.reports.sales_details', compact('branch', 'sources', 'sales'));
    }

    public function dayuse(Request $request)
    {
        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $sport_id = $request['sport_id'] ?? NULL;

        $members = Lead::whereType('member')
            ->with(['branch', 'sales_by', 'sport'])
            ->whereHas('memberships', function ($q) {
                $q->whereHas('service_pricelist', function ($y) {
                    $y->whereHas('service', fn ($q) => $q->whereName('Day Use'));
                });
            })
            ->withCount(['memberships' => fn ($q) => $q->whereHas('service_pricelist', function ($y) {
                $y->whereHas('service', fn ($q) => $q->whereName('Day Use'));
            })])
            ->when($branch_id, fn ($q) => $q->whereBranchId($branch_id))
            ->when($sport_id, fn ($q) => $q->whereSportId($sport_id))
            ->latest()
            ->get();

        return view('admin.reports.dayuse', compact('employee', 'branch_id', 'members'));
    }

    public function main_expired(Request $request)
    {
        $setting = Setting::firstOrFail();

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL)
        {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = isset($request['branch_id']) ? $request['branch_id'] : '';
        }

        $current_members = Lead::whereHas('memberships',fn($q) => $q
                                    ->whereIn('status',['current','expiring','pending'])
                                )
                                ->get();

        $expired_has_current_members = Lead::index($request->all())
                    ->whereType('member')
                    ->whereHas(
                        'memberships',fn($q) => $q
                                ->whereStatus('expired')
                                ->whereHas('service_pricelist.service.service_type',fn($y) => $y->whereIsPt(false))
                    )
                    ->with([
                        'branch',
                        'memberships' => fn ($q) => $q
                        ->whereHas('service_pricelist.service.service_type',fn($y) => $y->whereIsPt(false))
                        ->with([
                            'trainer', 'sales_by','service_pricelist.service.service_type' => fn($y) => $y->whereIsPt(false)
                        ])->orderBy('end_date','DESC'),
                    ])
                    ->withCount([
                        'memberships' => fn ($q) => $q
                                ->whereHas('service_pricelist.service.service_type', fn ($y) =>   $y->whereIsPt(false))
                    ])
                    ->latest()
                    ->get();

                    // dd($request->all());
        $members = $expired_has_current_members->diff($current_members);

        $sales      = User::whereRelation('roles','title','Sales')->orderBy('name')->pluck('name','id');

        $branches   = Branch::orderBy('name')->pluck('name','id');


        return view('admin.reports.main_expired', compact('setting','members', 'employee', 'branch_id','sales','branches'));
    }

    public function pt_expired(Request $request)
    {
        $setting = Setting::firstOrFail();

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL)
        {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = isset($request['branch_id']) ? $request['branch_id'] : '';
        }

        $current_members = Lead::whereHas('memberships',fn($q) => $q->whereIn('status',['current','expiring','pending']))
                                ->get();

        $expired_has_current_members = Lead::index($request->all())
                    ->whereType('member')
                    ->whereHas(
                        'memberships',fn($q) => $q
                            ->whereStatus('expired')
                            ->whereHas(
                                'service_pricelist.service.service_type',fn($y) => $y->whereIsPt(true)
                            )
                    )
                    ->with([
                        'branch',
                        'memberships' => fn ($q) => $q
                                ->whereHas('service_pricelist.service.service_type',fn($y) => $y->whereIsPt(true))
                                ->with([
                                    'trainer', 'sales_by','service_pricelist.service.service_type'
                                ])->orderBy('end_date','DESC'),
                    ])
                    ->withCount([
                        'memberships' => fn ($q) => $q
                                ->whereHas('service_pricelist.service.service_type', fn ($y) =>   $y->whereIsPt(true))
                    ])
                    ->latest()
                    ->get();

        $members = $expired_has_current_members->diff($current_members);

        // dd($members->whereHas('memberhsips'));

        $sales      = User::whereRelation('roles','title','Sales')->orderBy('name')->pluck('name','id');

        $branches   = Branch::orderBy('name')->pluck('name','id');

        return view('admin.reports.pt_expired', compact('setting','members', 'employee', 'branch_id','sales','branches'));
    }

    public function taxAccountant(Request $request)
    {
        $from = (!empty($request['created_at']) && !empty($request['created_at']['from']))  ? $request['created_at']['from'] : date('Y-m-01');
        $to = (!empty($request['created_at']) && !empty($request['created_at']['to']))  ? $request['created_at']['to'] : date('Y-m-t');

        $employee = Auth()->user()->employee;


        $accounts = [
            ''=>'All',
            'instapay' => 'Instapay',
            'cash' => 'Cash',
            'visa' => 'Visa',
            'vodafone' => 'Vodafone',
            'valu' => 'Valu',
            'premium' => 'Premium',
            'sympl' => 'Sympl'
        ];

        $accounts = $accounts + Account::where('name', 'NOT LIKE', '%cash%')
                ->where('name', 'NOT LIKE', '%vodafone%')
                ->orderBy('name')
                ->pluck('name', 'id')->toArray();


        $branches = Branch::pluck('name', 'id');
        if ($employee && $employee->branch_id != NULL)
        {
            $branch_id = $employee->branch_id;
            $branches = Branch::where('id',$branch_id)->pluck('name', 'id');
        } else {
            $branch_id = isset($request['branch_id']) ? $request['branch_id'] : '';
        }

        $data = $request->except('branch_id');
        $payments = Payment::index($data)
            ->with([
                'invoice',
                'invoice.membership',
                'invoice.membership.member',
                'invoice.membership.member.branch',
                'invoice.membership.service_pricelist',
                'account'
            ])
            ->whereHas('invoice', function ($q) {
                $q->where('status', '!=', 'refund')
                    ->whereHas('membership', function ($x) {
                        $x->whereHas('service_pricelist');
                    });
            })
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->whereHas('account', fn ($q) =>
                    $q->where('name', 'NOT LIKE', '%cash%')
                    ->where('name', 'NOT LIKE', '%vodafone%')
                // ->when($branch_id, fn ($y) => $y->whereIn('branch_id',$branch_id))
                    )
            // ->when($request['account_id'], fn ($q) => $q->whereIn('account_id',$request['account_id']))
            ->latest();

        if (!empty($branch_id)) {
            $branch_id = is_array($branch_id) ? $branch_id : [$branch_id];
            $branch_ids = Account::whereIn('branch_id', $branch_id)->pluck('id', 'name');
            $payments = $payments->whereIn('account_id', $branch_ids);
        }
        $payments = $payments->get();

        return view('admin.reports.tax_accountant', compact('payments', 'accounts', 'branches'));
    }

    public function taxAccountantExport(Request $request)
    {
        return Excel::download(new TaxAccountantExport($request->all()), 'tax-accountant.xlsx');
    }

    public function customerInvitation(Request $request)
    {
        $sport_id   = $request['sport_id'] ?? NULL;
        $trainer_id = $request['trainer_id'] ?? NULL;

        $leads = Lead::with(['trainer', 'sport'])
            ->whereInvitation(true)
            ->when($sport_id, fn ($q) => $q->whereSportId($sport_id))
            ->when($trainer_id, fn ($q) => $q->whereTrainerId($trainer_id))
            ->get();

        return view('admin.reports.customer_invitation', compact('leads'));
    }

//    public function all_due_payments(Request $request)
//    {
//        $employee = Auth()->user()->employee;
//
//        if ($employee && $employee->branch_id != NULL) {
//            $branch_id = $employee->branch_id;
//        } else {
//            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
//        }
//
//        $due_payments = Invoice::whereStatus('partial')->withSum('payments', 'amount')->latest()->get();
//
//        return view('admin.reports.all_due_payments', compact('due_payments', 'employee', 'branch_id'));
//    }
    public function all_due_payments(Request $request)
    {
        $employee = Auth()->user()->employee;

        $branch_id = $employee && $employee->branch_id ? $employee->branch_id : $request->branch_id;

        $branches = Branch::all();

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if ($start_date && !$end_date) {
            $end_date = now()->toDateString();
        }

        if (!$start_date && !$end_date) {
            $start_date = now()->startOfMonth()->toDateString();
            $end_date = now()->endOfMonth()->toDateString();
        }

        $due_payments = Invoice::whereStatus('partial')
            ->when($branch_id, function ($query) use ($branch_id) {
                return $query->whereHas('membership.member', function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id);
                });
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->withSum('payments', 'amount')
            ->latest()
            ->get();
        return view('admin.reports.all_due_payments', compact('due_payments', 'employee', 'branch_id', 'branches', 'start_date', 'end_date'));
    }


    public function sales_due_payments(Request $request)
    {
        $employee = Auth()->user()->employee;

        // Determine branch ID based on employee or request
        $branch_id = $employee && $employee->branch_id != NULL
            ? $employee->branch_id
            : ($request->branch_id != NULL ? $request->branch_id : '');


        // Fetch all branches for dropdown
        $branches = Branch::all();

        // Fetch all sales representatives for dropdown
        $sales_representatives = User::whereHas('roles', function ($query) {
            $query->where('role_id', 3); // Assuming '3' is the ID for the Sales role
        })->get();

        // Get the start and end dates from the request
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        // Handle default date ranges
        if ($start_date && !$end_date) {
            $end_date = now()->toDateString();
        }

        if (!$start_date && !$end_date) {
            $start_date = now()->startOfMonth()->toDateString();
            $end_date = now()->endOfMonth()->toDateString();
        }

        // Get selected sales representative ID from request
        $sales_id = $request->sales_id;

        // Build the query
        $due_payments = Invoice::whereStatus('partial')
            ->whereHas('membership', function ($query) {
                $query->whereHas('service_pricelist', function ($q) {
                    $q->whereHas('service', function ($q) {
                        $q->where('trainer', 0); // Filter out trainer services
                    });
                });
            })
            ->when($branch_id, function ($query) use ($branch_id) {
                return $query->whereHas('membership.member', function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id);
                });
            })
            ->when($sales_id, function ($query) use ($sales_id) {
                return $query->whereHas('sales_by', function ($q) use ($sales_id) {
                    $q->where('id', $sales_id);
                });
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->withSum('payments', 'amount')
            ->latest()
            ->get();

        return view('admin.reports.sales_due_payments', compact('due_payments', 'employee', 'branch_id', 'branches', 'sales_representatives', 'start_date', 'end_date'));
    }
    public function getSalesByBranch(Request $request)
    {
        $branch_id = $request->input('branch_id');

        $sales_representatives = User::whereHas('roles', function ($query) {
            $query->where('role_id', 3); // Ensure role_id is 3
        });


        if (!empty($branch_id)) {
            $sales_representatives = $sales_representatives->whereHas('employee', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            });
        }


        $sales_representatives = $sales_representatives->get();

        return response()->json($sales_representatives);
    }

    public function trainer_due_payments(Request $request)
    {
        $employee = Auth()->user()->employee;

        // Determine the branch ID
        $branch_id = $employee && $employee->branch_id != NULL
            ? $employee->branch_id
            : ($request->branch_id != NULL ? $request->branch_id : '');

        $branches = Branch::all();

        $trainers = User::whereHas('roles', function ($query) {
            $query->where('role_id', 2);
        })->get();

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if ($start_date && !$end_date) {
            $end_date = now()->toDateString();
        }

        if (!$start_date && !$end_date) {
            $start_date = now()->startOfMonth()->toDateString();
            $end_date = now()->endOfMonth()->toDateString();
        }

        $trainer_id = $request->trainer_id;

        $due_payments = Invoice::whereStatus('partial')
            ->whereHas('membership', function ($query) {
                $query->whereHas('service_pricelist', function ($q) {
                    $q->whereHas('service', function ($q) {
                        $q->where('trainer', 1);
                    });
                });
            })
            ->when($trainer_id, function ($query) use ($trainer_id) {
                return $query->whereHas('membership', function ($q) use ($trainer_id) {
                    $q->where('trainer_id', $trainer_id);
                });
            })

            ->when($branch_id, function ($query) use ($branch_id) {
                return $query->whereHas('membership.member', function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id);
                });
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->withSum('payments', 'amount')
            ->latest()
            ->get();

        return view('admin.reports.trainer_due_payments', compact('due_payments', 'employee', 'branch_id', 'branches', 'trainers', 'start_date', 'end_date'));
    }
    public function getTrainersByBranch(Request $request)
    {
        $branch_id = $request->input('branch_id');

        $trainersQuery = User::whereHas('roles', function ($query) {
            $query->where('role_id', 2);
        });


        if (!empty($branch_id)) {
            $trainersQuery->whereHas('employee', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            });
        }


        $trainers = $trainersQuery->get();

        return response()->json($trainers);
    }



    public function daily_task_report(Request $request)
    {
        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $from = isset($request['from']) ? $request['from'] : date('Y-m-d');
        $to = isset($request['to']) ? $request['to'] : date('Y-m-d');


        $reminder_sources   = LeadRemindersHistory::with([
            'lead' => fn ($q) => $q->with(['source', 'branch']),
            'membership' => fn ($q) => $q->with([
                'invoice'           => fn ($q) => $q->withSum('payments', 'amount'),
                'service_pricelist'
            ]),
            'user'
        ])
            ->whereHas(
                'lead',
                fn ($q) => $q
                    ->when($request['type'], fn ($y) => $y->whereType($request['type']))
                    ->when($branch_id, fn ($q) => $q->whereBranchId($branch_id))
            )
            ->whereHas('user.employee',fn($q) => $q->whereStatus('active'))
            ->when($request['sales_by_id'], fn ($q) => $q->whereUserId($request['sales_by_id']))
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        if (Auth()->user()->roles[0]->title == 'Sales'){
            $reminder_sources = $reminder_sources->where('user_id',Auth()->user()->id);
        }
        $reminder_sources = $reminder_sources->get();



        return view('admin.reports.sources', compact('employee', 'reminder_sources', 'branch_id'));
    }

    public function assignedCoachesReport(Request $request)
    {

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = isset($request['branch_id']) ? $request['branch_id'] : '';
        }
        if ($branch_id != null) {
            $non_pt_members =
                Membership::whereHas('member', function ($q) use ($request) {
                    $q->whereBranchId($request->branch_id);
                })
                ->whereHas('service_pricelist.service.service_type', fn ($q) => $q->where('is_pt', false))
                ->whereYear('start_date', date('Y'))
                ->whereMonth('start_date', date('m'))
                ->with([
                    'member', 'trainer', 'service_pricelist', 'sales_by', 'service_pricelist.service', 'service_pricelist.service.service_type', 'member.branch', 'sport'
                ])->where('assigned_coach_id', '!=', Null)

                ->withCount('attendances')
                ->withCount('trainer_attendances')
                ->get();
        } else {
            $non_pt_members =
                Membership::whereHas('service_pricelist.service.service_type', fn ($q) => $q->where('is_pt', false))
                ->whereYear('start_date', date('Y'))
                ->whereMonth('start_date', date('m'))
                ->with([
                    'member', 'trainer', 'service_pricelist', 'sales_by', 'service_pricelist.service', 'service_pricelist.service.service_type', 'member.branch', 'sport'
                ])->where('assigned_coach_id', '!=', Null)

                ->withCount('attendances')
                ->withCount('trainer_attendances')
                ->get();
        }



        return view('admin.reports.assigned_coaches', compact('non_pt_members', 'branch_id'));
    }


    // public  function sessions_revenue(Request $request)
    // {
    //     $month = $request->input('month') ?? '2023-07';
    //     $sessions = SessionList::query()->whereHas('trainer_attendants')->with('service')->get();


    //     $list = [];
    //     foreach ($sessions as $session) {
    //         $days = [];
    //         $session_name = '';
    //         $attendance = 0;
    //         $session_count  = 0;
    //         $schedules = Schedule::with(['session'])
    //             ->where('date', $month)
    //             ->where('session_id', $session->id)
    //             ->get();

    //         if ($schedules->count() != 0) {
    //             // dd($schedules[0]->date);
    //             // dd($month);
    //             foreach ($schedules as $schedule) {
    //                 $session_name = $schedule->session->name . ' - ' . $schedule->trainer->name;
    //                 $days[] = $schedule->day . '-' . $schedule->timeslot->from . '-' . $schedule->timeslot->to;
    //                 $session_count += TrainerAttendant::where('schedule_id', $schedule->id)
    //                     // ->whereMonth('created_at', explode('-', $month)[1])
    //                     // ->whereYear('created_at',  explode('-', $month)[0])
    //                     ->distinct(DB::raw('DATE(created_at)'))->count();

    //                 $trainer_attendants = TrainerAttendant::with(['schedule', 'membership', 'membership.invoice', 'membership.service_pricelist'])
    //                     ->whereHas('membership', fn ($q) => $q->whereHas('invoice'))
    //                     ->where('schedule_id', $schedule->id)
    //                     ->when($request->input('month'), function ($query) use ($request) {
    //                         $query->whereMonth('created_at', explode('-', $request->input('month'))[1])
    //                             ->whereYear('created_at', explode('-', $request->input('month'))[0]);
    //                     })
    //                     ->unless($request->input('month'), fn ($q) => $q->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y')))
    //                     ->get();
    //                 foreach ($trainer_attendants as $trainer_attendant) {
    //                     $attendance += 1;
    //                 }
    //             }
    //             $list[$session->id]['session']    = $session_name;
    //             $list[$session->id]['days']       = $days;
    //             $list[$session->id]['sessions_count'] = $session_count;
    //             $list[$session->id]['attendance']   =  $attendance;
    //         }
    //     }
    //     dd($list);
    //     return view('admin.reports.sessions_revenue', compact('schedules'));
    // }
    // public function sessions_revenue(Request $request)
    // {
    //     $month = $request->input('month') ?? '2023-05';
    //     $sessions_query = SessionList::query()->whereHas('trainer_attendants')->with('service');
    //     $session_ids = $sessions_query->pluck('id')->toArray();
    //     $sessions = $sessions_query->get();
    //     $schedules = Schedule::whereIn('session_id', $session_ids)->pluck('id')->toArray();
    //     $trainer_attendants = TrainerAttendant::with(['schedule', 'membership', 'membership.invoice', 'membership.service_pricelist'])
    //         ->whereHas('membership', fn ($q) => $q->whereHas('invoice'))
    //         ->whereIn('schedule_id', $schedules)
    //         ->when($request->input('month'), function ($query) use ($request) {
    //             $query->whereMonth('created_at', explode('-', $request->input('month'))[1])
    //                 ->whereYear('created_at', explode('-', $request->input('month'))[0]);
    //         })
    //         ->unless($request->input('month'), fn ($q) => $q->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y')))
    //         ->get();
    //     $report = [];
    //     $sechedule_name  = [];
    //     foreach ($sessions as $sess) {
    //         $sc = Schedule::whereSessionId($sess->id)->get();
    //         foreach ($sc as $key => $sched) {
    //             $schedulesNames = array_column($report, 'name');
    //             if (in_array($sched->session->name . '-' . $sched->trainer->name, $schedulesNames)) {
    //             } else {
    //                 $report[$sched->id]  = [
    //                     'session_id'        =>  $sched->session->id,
    //                     'name'          => $sched->session->name . '-' . $sched->trainer->name,
    //                     'attendants'        => 0,
    //                     'sessions_count'    => 0,
    //                     'revenue'           => 0,
    //                     'utilization_rate'  => $sess->ranking,
    //                     'session'           => [
    //                         'name'          => $sched->session->name . '-' . $sched->trainer->name,
    //                         'color'         => $sess->color,
    //                         'max_capacity'  => $sess->max_capacity,
    //                         'ranking'       => $sess->ranking
    //                     ]
    //                 ];

    //                 $session_count = TrainerAttendant::where('schedule_id', $sched->id)
    //                     ->whereMonth('created_at', explode('-', $month)[1])
    //                     ->whereYear('created_at', explode('-', $month)[0])
    //                     // ->unless($month, fn ($q) => $q->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y')))
    //                     ->get();
    //                 // dd($session_count);

    //                 if ($report[$sched->id]['attendants'] > 0) {
    //                     $sched->append(['ranking' => round(($report[$sched->id]['attendants'] / ($report[$sched->id]['sessions_count'] * $report[$sched->id]['session']['max_capacity'])) * 100)]);
    //                 }

    //                 $report[$sched->id]['utilization_rate'] = $report[$sched->id]['attendants'] > 0 ? round(($report[$sched->id]['attendants'] / ($report[$sched->id]['sessions_count'] * $report[$sched->id]['session']['max_capacity'])) * 100, 2) : 0;
    //             }
    //         }
    //     }

    //     foreach ($trainer_attendants as $trainer_attendant) {
    //         // dd($trainer_attendant);
    //         if (in_array($trainer_attendant->schedule_id, $report)) {
    //             $report[$trainer_attendant->schedule_id]['attendants'] += 1;
    //             $report[$trainer_attendant->schedule_id]['revenue'] += $trainer_attendant->membership->invoice->net_amount;
    //         }
    //     }
    //     // dd($report);
    //     // foreach ($sessions as $session) {

    //     //     $report[$session->id]['sessions_count'] = TrainerAttendant::whereIn('schedule_id', $session->schedules()->pluck('id')->toArray())
    //     //         ->when($request->input('month'), function ($query) use ($request) {
    //     //             $query->whereMonth('created_at', explode('-', $request->input('month'))[1])
    //     //                 ->whereYear('created_at', explode('-', $request->input('month'))[0]);
    //     //         })
    //     //         ->unless($request->input('month'), fn ($q) => $q->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y')))
    //     //         ->distinct(DB::raw('DATE(created_at)'))->count();


    //     //     if ($report[$session->id]['attendants'] > 0) {
    //     //         $session->append(['ranking' => round(($report[$session->id]['attendants'] / ($report[$session->id]['sessions_count'] * $report[$session->id]['session']['max_capacity'])) * 100)]);
    //     //     }

    //     //     $report[$session->id]['utilization_rate'] = $report[$session->id]['attendants'] > 0 ? round(($report[$session->id]['attendants'] / ($report[$session->id]['sessions_count'] * $report[$session->id]['session']['max_capacity'])) * 100, 2) : 0;
    //     // }
    //     // dd($report);
    //     return view('admin.reports.sessions_revenue', compact('sessions', 'report'));
    // }
    // public function sessions_revenue(Request $request)
    // {
    //     $month = $request->input('month') ?? '2023-05';

    //     $sessions_query = SessionList::query()->whereHas('trainer_attendants')->with('service');
    //     $session_ids = $sessions_query->pluck('id')->toArray();
    //     $sessions = $sessions_query->get();
    //     $schedules = Schedule::whereIn('session_id', $session_ids)->pluck('id')->toArray();
    //     $trainer_attendants = TrainerAttendant::with(['schedule', 'membership', 'membership.invoice', 'membership.service_pricelist'])
    //         ->whereHas('membership', fn ($q) => $q->whereHas('invoice'))
    //         ->whereIn('schedule_id', $schedules)
    //         ->whereMonth('created_at', explode('-', $month)[1])
    //         ->whereYear('created_at', explode('-', $month)[0])
    //         ->get();
    //     $report = [];
    //     foreach ($sessions as $sess) {
    //         $report[$sess->id] = [
    //             'session_id'        => $sess->id,
    //             'attendants'        => 0,
    //             'sessions_count'    => 0,
    //             'revenue'           => 0,
    //             'utilization_rate'  => $sess->ranking,
    //             'session'           => [
    //                 'name'          => $sess->name,
    //                 'color'         => $sess->color,
    //                 'max_capacity'  => $sess->max_capacity,
    //                 'ranking'       => $sess->ranking
    //             ]
    //         ];
    //     }
    //     foreach ($trainer_attendants as $trainer_attendant) {
    //         $report[$trainer_attendant->schedule->session_id]['attendants'] += 1;
    //         $report[$trainer_attendant->schedule->session_id]['revenue'] += $trainer_attendant->membership->invoice->net_amount;
    //     }
    //     foreach ($sessions as $session) {

    //         $report[$session->id]['sessions_count'] = TrainerAttendant::whereIn('schedule_id', $session->schedules()->pluck('id')->toArray())
    //             ->whereMonth('created_at', explode('-', $month)[1])
    //             ->whereYear('created_at', explode('-', $month)[0])
    //             ->distinct(DB::raw('DATE(created_at)'))->count();
    //     }
    //     dd($report);
    //     return view('admin.reports.sessions_revenue', compact('list'));
    // }

    public function sessions_revenue(Request $request)
    {
        $month = $request->input('month') ?? '2023-05';
        $list = [];
        $schedule_mains = ScheduleMain::where('date', $month)->get();
        foreach ($schedule_mains as $schedule_main) {
            $days = [];
            foreach ($schedule_main->schedules as $value) {
                $days[] = $value->day . '-' . $value->timeslot->from . '-' . $value->timeslot->to;

            }
            $list[$schedule_main->id] = [
                'session'   => $schedule_main->session->name . '-' . $schedule_main->trainer->name,
                'days'      => $days
            ];
        }
        // dd($list);
        return view('admin.reports.sessions_revenue', compact('list'));
    }

    public function guest_log(Request $request)
    {
        $from   = isset($request['from']) ? $request['from'] : date('Y-m-01');
        $to     = isset($request['to']) ? $request['to'] : date('Y-m-t');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $latest_leads = Lead::with(['memberships.service_pricelist', 'invoices.payments', 'source','sales_by','branch'])
            ->when($branch_id, fn($q) => $q->whereBranchId($request['branch_id']))
            ->when($request['source_id'], fn($q) => $q->whereSourceId($request['source_id']))
            ->when($request['sales_by_id'], fn($q) => $q->whereSalesById($request['sales_by_id']))
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<',$to)
            ->latest()
            ->get()
            ->groupBy('source.name');

        return view('admin.reports.guest_log',compact('latest_leads','employee','branch_id'));
    }

    public function export_guest_log(Request $request)
    {
        return Excel::download(new GuestLogExport($request->all()), 'Guest-log-report-' . $request['date'] . '.xlsx');
    }

    public function action_report(Request $request)
    {
        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL)
        {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $from = isset($request['from']) ? $request['from'] : date('Y-m-01');
        $to = isset($request['to']) ? $request['to'] : date('Y-m-t');

        $reminder_actions   = Reminder::with([
                'lead' => fn ($q) => $q->with(['source', 'branch']),
                'membership' => fn ($q) => $q->with([
                    'invoice'           => fn ($q) => $q->withSum('payments', 'amount'),
                    'service_pricelist'
                ]),
                'user'
            ])
            ->whereHas(
                'lead',
                fn ($q) => $q
                    ->when($request['type'], fn ($y) => $y->whereType($request['type']))
                    ->when($branch_id, fn ($q) => $q->whereBranchId($branch_id))
            )
            ->when($request['sales_by_id'], fn ($q) => $q->whereUserId($request['sales_by_id']))
            ->when($request['reminder_action'], fn ($q) => $q->whereAction($request['reminder_action']))
            ->whereNotIn('type',['pt_session'])
            ->whereDate('due_date', '>=', $from)
            ->whereDate('due_date', '<=', $to);

        if (Auth()->user()->roles[0]->title == 'Sales'){
            $reminder_actions = $reminder_actions->where('user_id',Auth()->user()->id);
        }
            $reminder_actions = $reminder_actions->get();

        return view('admin.reports.actions', compact('employee', 'branch_id','reminder_actions'));
    }

    public function export_actions_report(Request $request)
    {
        return Excel::download(new ActionsReportExport($request->all()), 'Actions-report-' . $request['date'] . '.xlsx');
    }

    public function trainers_reminder_actions(Request $request)
    {
        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL)
        {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $from = isset($request['from']) ? $request['from'] : date('Y-m-01');
        $to = isset($request['to']) ? $request['to'] : date('Y-m-t');

        if (Auth()->user()->roles[0]->title == 'Trainer')
        {
            $reminder_actions   = Reminder::with([
                    'lead' => fn ($q) => $q->with(['source', 'branch']),
                    'membership' => fn ($q) => $q->with([
                        'invoice'           => fn ($q) => $q->withSum('payments', 'amount'),
                        'service_pricelist'
                    ]),
                    'user'
                ])
                ->whereHas(
                    'lead',
                    fn ($q) => $q
                        ->when($request['type'], fn ($y) => $y->whereType($request['type']))
                        ->when($branch_id, fn ($q) => $q->whereBranchId($branch_id)),
                )
                ->whereHas('user',fn($q) => $q->whereRelation('roles','title','Trainer')->whereUserId(Auth()->id()))
                ->when($request['reminder_action'], fn ($q) => $q->whereAction($request['reminder_action']))
                ->whereIn('type',['pt_session'])
                ->whereDate('due_date', '>=', $from)
                ->whereDate('due_date', '<=', $to)
                ->get();
        }else{
            $reminder_actions   = Reminder::with([
                'lead' => fn ($q) => $q->with(['source', 'branch']),
                'membership' => fn ($q) => $q->with([
                    'invoice'           => fn ($q) => $q->withSum('payments', 'amount'),
                    'service_pricelist'
                ]),
                'user'
            ])
            ->whereHas(
                'lead',
                fn ($q) => $q
                    ->when($request['type'], fn ($y) => $y->whereType($request['type']))
                    ->when($branch_id, fn ($q) => $q->whereBranchId($branch_id)),
            )
            ->whereHas('user',fn($q) => $q->whereRelation('roles','title','Trainer')->when($request['trainer_id'], fn ($q) => $q->whereUserId($request['trainer_id'])))
            ->when($request['reminder_action'], fn ($q) => $q->whereAction($request['reminder_action']))
            ->whereIn('type',['pt_session'])
            ->whereDate('due_date', '>=', $from)
            ->whereDate('due_date', '<=', $to)
            ->get();
        }



        return view('admin.reports.trainers_reminder_actions', compact('employee', 'branch_id','reminder_actions'));
    }

    public function trainers_reminder_history_actions(Request $request)
    {
        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL)
        {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $from = isset($request['from']) ? $request['from'] : date('Y-m-01');
        $to = isset($request['to']) ? $request['to'] : date('Y-m-t');

        if (Auth()->user()->roles[0]->title == 'Trainer')
        {
            $reminder_history_actions  = LeadRemindersHistory::with([
                'lead' => fn ($q) => $q->with(['source', 'branch']),
                'membership' => fn ($q) => $q->with([
                    'invoice'           => fn ($q) => $q->withSum('payments', 'amount'),
                    'service_pricelist'
                ]),
                'user'
            ])
            ->whereHas(
                'lead',
                fn ($q) => $q
                    ->when($request['type'], fn ($y) => $y->whereType($request['type']))
                    ->when($branch_id, fn ($q) => $q->whereBranchId($branch_id)),
            )
            ->whereHas('user',fn($q) => $q
                        ->whereHas('employee',fn($q) => $q->whereStatus('active'))
                        ->whereRelation('roles','title','Trainer')
                        ->whereUserId(Auth()->id())
            )
            ->when($request['reminder_action'], fn ($q) => $q->whereAction($request['reminder_action']))
            ->whereDate('due_date', '>=', $from)
            ->whereDate('due_date', '<=', $to)
            ->get();
        }else{
            $reminder_history_actions  = LeadRemindersHistory::with([
                'lead' => fn ($q) => $q->with(['source', 'branch']),
                'membership' => fn ($q) => $q->with([
                    'invoice'           => fn ($q) => $q->withSum('payments', 'amount'),
                    'service_pricelist'
                ]),
                'user'
            ])
            ->whereHas(
                'lead',
                fn ($q) => $q
                    ->when($request['type'], fn ($y) => $y->whereType($request['type']))
                    ->when($branch_id, fn ($q) => $q->whereBranchId($branch_id)),
            )
            ->whereHas('user',fn($q) => $q
                                ->whereHas('employee',fn($q) => $q->whereStatus('active'))
                                ->whereRelation('roles','title','Trainer')
                                ->when($request['trainer_id'], fn ($q) => $q->whereUserId($request['trainer_id']))
                )
            ->when($request['reminder_action'], fn ($q) => $q->whereAction($request['reminder_action']))
            ->whereDate('due_date', '>=', $from)
            ->whereDate('due_date', '<=', $to)
            ->get();
        }

        return view('admin.reports.trainers_reminder_history_actions', compact('employee', 'branch_id','reminder_history_actions'));
    }

    public function trainer_reminders(Request $request)
    {
        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $trainers = User::whereRelation('roles', 'title', 'Trainer')
            ->whereHas('employee', fn ($q) => $q->whereStatus('active')->when($branch_id, fn ($y) => $y->whereBranchId($branch_id)))
            ->orderBy('name')
            ->whereHas('reminders')
            ->with(['reminders', 'todayReminders', 'upcommingReminders', 'overdueReminders'])
            ->withCount(['reminders', 'todayReminders', 'upcommingReminders', 'overdueReminders'])
            ->get();

        return view('admin.reports.trainer_reminders', compact('trainers', 'employee', 'branch_id'));
    }

    public function fitness_manager(Request $request)
    {
        $from   = isset($request['from']) ? $request['from'] : date('Y-m-01');
        $to     = isset($request['to']) ? $request['to'] : date('Y-m-t');

        $fitness_managers = User::whereRelation('roles','title','Fitness Manager')
                        ->with(['employee.branch'])
                        ->whereHas('employee',fn($q) => $q->whereStatus('active'))
                        ->get()
                        ->map(function($fitness_manager) use ($from,$to){
                            $memberships_count = Membership::whereHas(
                                    'member',fn($q) => $q->whereBranchId($fitness_manager->employee->branch_id)
                                )
                                ->whereHas('service_pricelist.service.service_type',fn($q) => $q->where('is_pt',false))
                                ->whereIn('status',['current','expiring','pending'])
                                ->whereDate('created_at','>=',$from)
                                ->whereDate('created_at','<=',$to)
                                ->count();

                            $unassigned_memberships_count = Membership::whereHas(
                                    'member',fn($q) => $q->whereBranchId($fitness_manager->employee->branch_id)
                                )
                                ->whereHas('service_pricelist.service.service_type',fn($q) => $q->where('is_pt',false))
                                ->whereDoesntHave('assigned_coach')
                                ->whereIn('status',['current','expiring','pending'])
                                ->whereDate('created_at','>=',$from)
                                ->whereDate('created_at','<=',$to)
                                ->count();

                            $assigned_memberships_count = Membership::whereHas(
                                    'member',fn($q) => $q->whereBranchId($fitness_manager->employee->branch_id)
                                )
                                ->whereHas('service_pricelist.service.service_type',fn($q) => $q->where('is_pt',false))
                                ->whereHas('assigned_coach')
                                ->whereIn('status',['current','expiring','pending'])
                                ->whereDate('created_at','>=',$from)
                                ->whereDate('created_at','<=',$to)
                                ->count();

                            return [
                                'id'                            => $fitness_manager->id,
                                'name'                          => $fitness_manager->name,
                                'branch_name'                   => $fitness_manager->employee->branch->name ?? '-',
                                'memberships_count'             => $memberships_count ?? 0,
                                'assigned_memberships_count'    => $assigned_memberships_count ?? 0,
                                'unassigned_memberships_count'  => $unassigned_memberships_count ?? 0,
                            ];
                        });

        return view('admin.reports.fitness_manager',compact('fitness_managers'));
    }

    public function show_fitness_manager(Request $request,User $fitness_manager)
    {
        $from   = isset($request['from']) ? $request['from'] : date('Y-m-01');
        $to     = isset($request['to']) ? $request['to'] : date('Y-m-t');
        $trainer_id = isset($request['trainer_id']) ? $request['trainer_id'] : NULL;

        $fitness_manager = User::whereRelation('roles','title','Fitness Manager')
            ->with(['employee.branch'])
            ->findOrFail($fitness_manager->id);

        $memberships = Membership::whereHas('member',fn($q) => $q->whereBranchId($fitness_manager->employee->branch_id))
            ->whereHas('service_pricelist.service.service_type',fn($q) => $q->where('is_pt',false))
            ->whereIn('status',['current','expiring','pending'])
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->when($trainer_id,fn($q) => $q->whereAssignedCoachId($trainer_id))
            ->get();

        $unassigned_memberships = Membership::whereHas(
                'member',fn($q) => $q->whereBranchId($fitness_manager->employee->branch_id)
            )
            ->whereHas('service_pricelist.service.service_type',fn($q) => $q->where('is_pt',false))
            ->whereDoesntHave('assigned_coach')
            ->whereIn('status',['current','expiring','pending'])
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->when($trainer_id,fn($q) => $q->whereAssignedCoachId($trainer_id))
            ->get();

        $assigned_memberships = Membership::whereHas(
                'member',fn($q) => $q->whereBranchId($fitness_manager->employee->branch_id)
            )
            ->whereHas('service_pricelist.service.service_type',fn($q) => $q->where('is_pt',false))
            ->whereHas('assigned_coach')
            ->whereIn('status',['current','expiring','pending'])
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->when($trainer_id,fn($q) => $q->whereAssignedCoachId($trainer_id))
            ->orderByDesc('assign_date')
            ->get();

        $trainers = User::whereRelation('roles','title','Trainer')
                            ->whereHas('employee',fn($q) => $q->whereStatus('active')->whereBranchId($fitness_manager->employee->branch_id))
                            ->orderBy('name')
                            ->pluck('name','id');

        return view('admin.reports.fitness_manager_show',compact('fitness_manager','memberships','unassigned_memberships','assigned_memberships','trainers'));
    }

    public function pt_attendances(Request $request)
    {
        $from   = isset($request['from']) ? $request['from'] : date('Y-m-01');
        $to     = isset($request['to']) ? $request['to'] : date('Y-m-t');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL)
        {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }


        $membership_attendances = MembershipAttendance::with([
                        'membership' => fn($q) => $q->with(['member.branch','assigned_coach','service_pricelist']),
                        'branch'
                    ])
                    ->whereHas('membership.service_pricelist.service.service_type',fn($q) => $q->where('is_pt',true))
                    ->whereDate('created_at','>=',$from)
                    ->whereDate('created_at','<=',$to)
                    ->when($branch_id,fn($q) => $q->whereBranchId($branch_id))
                    ->latest()
                    ->get();

        return view('admin.reports.pt_attendances',compact('membership_attendances','employee','branch_id'));
    }

    public function sales_daily(Request $request)
    {

        $from   = isset($request['from']) ? $request['from'] : date('Y-m-01');
        $to     = isset($request['to']) ? $request['to'] : date('Y-m-t');

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL)
        {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }
        $type = Auth()->user()->roles[0]->title;

        $sales_service          = new SalesService;
        $invoices               = $sales_service->invoices($from,$to,$branch_id,$type)->sum('net_amount');
        $payments_sum_amount    = $sales_service->invoices($from,$to,$branch_id,$type)->sum('payments_sum_amount');
        $refunds                = $sales_service->refunds($from,$to,$branch_id,$type)->sum('amount');
        $pending                = $invoices - $payments_sum_amount;
        $payments               = $sales_service->payments($from,$to,$branch_id,$type)->sum('amount');
        $service_payments       = $sales_service->service_payments($from,$to,$branch_id,$type);
        $service_refunds        = $sales_service->service_refunds($from,$to,$branch_id,$type);

        return view('admin.reports.sales_daily',compact('employee','branch_id','invoices','payments_sum_amount','refunds','pending','payments','service_payments','service_refunds'));
    }

    public function trainer_daily(Request $request)
    {
        $from   = isset($request['from']) ? $request['from'] : date('Y-m-01');
        $to     = isset($request['to']) ? $request['to'] : date('Y-m-t');

        $employee = Auth()->user()->employee;
//        dd(Auth()->user()->id);
        if ($employee && $employee->branch_id != NULL)
        {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $trainer_service = new TrainerService;
        $invoices               = $trainer_service->invoices($from,$to,$branch_id)->sum('net_amount');
        $payments_sum_amount    = $trainer_service->invoices($from,$to,$branch_id)->sum('payments_sum_amount');
        $refunds                = $trainer_service->refunds($from,$to,$branch_id)->sum('amount');
        $pending                = $invoices - $payments_sum_amount;
        $payments               = $trainer_service->payments($from,$to,$branch_id)->sum('amount');
        $service_payments       = $trainer_service->service_payments($from,$to,$branch_id);
        $service_refunds        = $trainer_service->service_refunds($from,$to,$branch_id);

        return view('admin.reports.trainer_daily',compact('employee','branch_id','invoices','payments_sum_amount','refunds','pending','payments','service_payments','service_refunds'));
    }


    public function previous_month_report(Request $request){
        $date =  date('Y-m');

        $from   =  date('Y-m-01');
        $to     =  date('Y-m-t');

        $today = Carbon::now('UTC');
        $today2 = Carbon::now('UTC');
        $startOfLastMonth = $today->subMonth()->startOfMonth()->toDateString();
        $endOfLastMonth = $today2->subMonth()->toDateString();


        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL)
        {
            $branch_id = $employee->branch_id;
        }
        else
        {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $branches = Branch::with([
            'accounts',
            'transactions' => fn ($q) => $q->whereYear('transactions.created_at', date('Y', strtotime($date)))
                ->whereMonth('transactions.created_at', date('m', strtotime($date)))
        ]);
        $lastMonthBranchesTransactions = Branch::with(['transactions' => function($query) use ($startOfLastMonth, $endOfLastMonth, $today ,$today2) {
            $query->whereDate('transactions.created_at', '>=', $startOfLastMonth)->whereDate('transactions.created_at', '<=', $endOfLastMonth);
        }]);

        if ($branch_id != ''){
            $branches = $branches->where('id',$branch_id);
            $lastMonthBranchesTransactions = $lastMonthBranchesTransactions->where('id',$branch_id);
        }
        $branches = $branches->get();
        $lastMonthBranchesTransactions = $lastMonthBranchesTransactions->get();
        //Over All Report



        //Sales Report
        $sales_service          = new SalesService;
        $invoices               = $sales_service->invoices($startOfLastMonth,$endOfLastMonth,$branch_id)->sum('net_amount');
        $payments_sum_amount    = $sales_service->invoices($startOfLastMonth,$endOfLastMonth,$branch_id)->sum('payments_sum_amount');
        $refunds                = $sales_service->refunds($startOfLastMonth,$endOfLastMonth,$branch_id)->sum('amount');
        $pending                = $invoices - $payments_sum_amount;
        $payments               = $sales_service->payments($startOfLastMonth,$endOfLastMonth,$branch_id)->sum('amount');

        $current_month_sales_service          = new SalesService;
        $current_month_invoices               = $current_month_sales_service->invoices($from,$to,$branch_id)->sum('net_amount');
        $current_month_payments_sum_amount    = $current_month_sales_service->invoices($from,$to,$branch_id)->sum('payments_sum_amount');
        $current_month_refunds                = $current_month_sales_service->refunds($from,$to,$branch_id)->sum('amount');
        $current_month_pending                = $current_month_invoices - $current_month_payments_sum_amount;
        $current_month_payments               = $current_month_sales_service->payments($from,$to,$branch_id)->sum('amount');
     
        //Traineer Reports
        $trainer_service = new TrainerService;
        $trainer_invoices               = $trainer_service->invoices($startOfLastMonth,$endOfLastMonth,$branch_id)->sum('net_amount');
        $trainer_payments_sum_amount    = $trainer_service->invoices($startOfLastMonth,$endOfLastMonth,$branch_id)->sum('payments_sum_amount');
        $trainer_refunds                = $trainer_service->refunds($startOfLastMonth,$endOfLastMonth,$branch_id)->sum('amount');
        $trainer_pending                = $trainer_invoices - $trainer_payments_sum_amount;
        $trainer_payments               = $trainer_service->payments($startOfLastMonth,$endOfLastMonth,$branch_id)->sum('amount');

        $current_month_trainer_service = new TrainerService;
        $current_month_trainer_invoices               = $current_month_trainer_service->invoices($from,$to,$branch_id)->sum('net_amount');
        $current_month_trainer_payments_sum_amount    = $current_month_trainer_service->invoices($from,$to,$branch_id)->sum('payments_sum_amount');
        $current_month_trainer_refunds                = $current_month_trainer_service->refunds($from,$to,$branch_id)->sum('amount');
        $current_month_trainer_pending                = $current_month_trainer_invoices - $current_month_trainer_payments_sum_amount;
        $current_month_trainer_payments               = $trainer_service->payments($from,$to,$branch_id)->sum('amount');

        

       
        return view('admin.reports.prev_month_report', compact('branches','lastMonthBranchesTransactions','startOfLastMonth','endOfLastMonth' ,'employee','branch_id','invoices','payments_sum_amount','refunds','pending','payments','current_month_invoices','current_month_payments_sum_amount','current_month_refunds','current_month_pending','current_month_payments' ,'trainer_invoices','trainer_payments_sum_amount','trainer_refunds','trainer_pending','trainer_payments'   ,'current_month_trainer_invoices','current_month_trainer_payments_sum_amount','current_month_trainer_refunds','current_month_trainer_pending','current_month_trainer_payments'));
    }

}
