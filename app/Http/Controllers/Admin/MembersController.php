<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sms;
use App\Models\Lead;
use App\Models\User;
use App\Models\Sport;
use App\Models\Source;
use App\Models\Status;
use App\Models\Account;
use App\Models\Address;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Setting;
use Twilio\Rest\Client;
use App\Models\Reminder;
use App\Models\Marketing;
use App\Models\Pricelist;
use App\Models\Invitation;
use App\Models\Membership;
use App\Models\Transaction;
use App\Models\MemberStatus;
use Illuminate\Http\Request;
use App\Models\FreezeRequest;
use App\Exports\MembersExport;
use App\Models\MemberReminder;
use App\Models\TrackMembership;
use App\Imports\SalesDataImport;
use App\Exports\InvitationsExport;
use Illuminate\Support\Facades\DB;
use App\Exports\ActiveMembersExport;
use App\Http\Controllers\Controller;
use App\Models\LeadRemindersHistory;
use App\Models\MembershipAttendance;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InactiveMembersExport;
use App\Exports\OnholdExport;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\MassDestroyLeadRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\Branch;
use App\Models\MembershipSchedule;
use App\Models\PopMessage;
use App\Models\PopMessageReply;
use App\Models\ScheduleMain;
use App\Models\SessionList;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MembersController extends Controller
{
    use MediaUploadingTrait;
    use CsvImportTrait;

    public function index(Request $request)
    {
        
        abort_if(Gate::denies('member_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);
        
        $settings = Setting::first();

        $employee = Auth()->user()->employee;

        $user = Auth()->user();

        if ($request->ajax()) {
            // if ($employee && $employee->branch_id != NULL) {
            //     if ($user->roles[0]->title == 'Sales') 
            //     {
            //         $query = Lead::index($data)
            //             ->with(['status', 'source', 'sales_by', 'address', 'created_by', 'branch'])
            //             ->whereType('member')
            //             ->whereBranchId($employee->branch_id)
            //             ->whereSalesById($user->id)
            //             ->orderBy('member_code', 'desc')
            //             ->select(sprintf('%s.*', (new Lead())->table));
            //     } else {
            //         $query = Lead::index($data)
            //             ->with(['status', 'source', 'sales_by', 'address', 'created_by', 'branch'])
            //             ->whereType('member')
            //             ->whereBranchId($employee->branch_id)
            //             ->orderBy('member_code', 'desc')
            //             ->select(sprintf('%s.*', (new Lead())->table));
            //     }
            // } else {
                $query = $employee->branch_id ? Lead::index($data)
                    ->with(['status', 'source', 'sales_by', 'address', 'created_by', 'branch'])
                    ->whereType('member')
                    ->whereBranchId($employee->branch_id)
                    ->orderBy('member_code', 'desc')
                    ->select(sprintf('%s.*', (new Lead())->table)):Lead::index($data)
                    ->with(['status', 'source', 'sales_by', 'address', 'created_by', 'branch'])
                    ->whereType('member')
                    ->orderBy('member_code', 'desc')
                    ->select(sprintf('%s.*', (new Lead())->table));
            // }

            $table = Datatables::eloquent($query);
            
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'member_show';
                $editGate = 'member_edit';
                $deleteGate = 'member_delete';
                $crudRoutePart = 'members';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) use ($settings) {
                return $row->id ? ($row->branch ? $row->branch->member_prefix : '') ?? 'ZN' . $row->id : '';
            });

            $table->editColumn('notes', function ($row) {
                return $row->notes != NULL ? $row->notes : trans('global.no_data_available');
            });

            $table->editColumn('photo', function ($row) use ($settings) {
                if ($photo = $row->photo) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px" class="rounded-circle"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                } else {
                    $setting = $settings->menu_logo;

                    return '<a href="' . route('admin.members.show', $row->id) . '" target="_blank">
                    <img src="' . asset('images/' . $setting) . '" width="50px" height="50px" class="rounded-circle">
                </a>';
                }
            });


            $table->editColumn('name', function ($row) use ($settings) {
                return $row->name ? ($row->branch ? $row->branch->member_prefix : '') . $row->member_code . '<br>' . '<a href="' . route('admin.members.show', $row->id) . '" target="_blank">' . $row->name . '</a>'
                    . '<br/>' . '<b>' . $row->phone . '<b>'
                    . '<br/>' . '<b>' . Lead::GENDER_SELECT[$row->gender] . '</b>' : '';
            });

            // $table->editColumn('phone', function ($row) {
            //     return $row->phone ? '<a href="'.route('admin.members.show',$row->id).'" target="_blank">'.$row->phone.'</a>' : '';
            // });

            $table->editColumn('national', function ($row) {
                return $row->national ? $row->national : '';
            });

            $table->editColumn('member_code', function ($row) use ($settings) {
                return $row->member_code ? ($row->branch ? $row->branch->member_prefix : '') . $row->member_code : '';
            });

            $table->addColumn('status_name', function ($row) {
                return $row->status ? "<span class='badge badge-" . $row->status->color . " px-2 py-2'>" . $row->status->name . "</span>" : '';
            });

            // $table->addColumn('address_name', function ($row) {
            //     return $row->address ? $row->address->name : '';
            // });

            $table->addColumn('branch_name', function ($row) {
                return $row->branch ? $row->branch->name : '';
            });

            $table->addColumn('source_name', function ($row) {
                return $row->source ? $row->source->name : '';
            });

            $table->editColumn('gender', function ($row) {
                return $row->gender ? Lead::GENDER_SELECT[$row->gender] : '';
            });
            $table->editColumn('downloaded_app', function ($row) {
                return $row->downloaded_app == true ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
            });

            $table->addColumn('sales_by_name', function ($row) {
                return $row->sales_by ? $row->sales_by->name : '';
            });

            $table->addColumn('created_by', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->addColumn('email', function ($row) {
                return $row->user ? $row->user->email : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->editColumn('type', function ($row) {
                return $row->type ? Lead::TYPE_SELECT[$row->type] : '';
            });

            $table->rawColumns(['actions', 'name', 'phone', 'placeholder', 'photo', 'status', 'source', 'downloaded_app', 'sales_by', 'id', 'created_by', 'email', 'status_name', 'branch_name']);

            return $table->make(true);
        }
        $branches =$employee->branch_id ? Branch::where('id', $employee->branch_id)->pluck('name', 'id'):Branch::pluck('name', 'id');

        $statuses = Status::pluck('name', 'id');

        $sources = Source::pluck('name', 'id');

        $addresses = Address::pluck('name', 'id');

        $sales = $employee->branch_id ? User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->whereHas('employee', function ($q) use($employee) {
                $q->whereHas('branch', function ($x) use($employee) {
                    $x->where('id', $employee->branch_id); // Use where for a single value
                });
            })
            ->pluck('name', 'id'):User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->pluck('name', 'id');


        if ($employee && $employee->branch_id != NULL) {
            if ($user->roles[0]->title == 'Sales') {
                $members = Lead::index($data)
                    ->whereType('member')
                    ->whereSalesById(Auth()->id())
                    ->whereBranchId($employee->branch_id)
                    ->count();

                $today_birthdays = Lead::whereType('member')
                    ->whereBranchId($employee->branch_id)
                    ->whereSalesById(Auth()->id())
                    ->whereMonth('dob', date('m'))->whereDay('dob', date('d'))
                    ->get(['name', 'id', 'dob']);
            } else {
                $members = Lead::index($data)
                    ->whereType('member')
                    ->whereBranchId($employee->branch_id)
                    ->count();

                $today_birthdays = Lead::whereType('member')
                    ->whereBranchId($employee->branch_id)
                    ->whereMonth('dob', date('m'))->whereDay('dob', date('d'))
                    ->get(['name', 'id', 'dob']);
            }
        } else {
            $members = Lead::index($data)->whereType('member')->count();

            $today_birthdays = Lead::whereType('member')->whereMonth('dob', date('m'))->whereDay('dob', date('d'))->get(['name', 'id', 'dob']);
        }

        return view('admin.members.index', compact('statuses', 'sources', 'addresses', 'sales', 'members', 'today_birthdays', 'branches','data'));
    }

    public function create()
    {
        abort_if(Gate::denies('member_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $selected_branch = isset(Auth()->user()->employee) ? Auth()->user()->employee->branch : Null;

        $statuses = Status::pluck('name', 'id');

        $sources = Source::pluck('name', 'id');

        $addresses = Address::pluck('name', 'id');

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


        $last_invoice = Invoice::latest()->first()->id ?? 1;

        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');


        $last_member_code = Lead::whereType('member')->when($selected_branch, function ($q) use ($selected_branch) {
            $q->whereBranchId($selected_branch->id);
        })->whereDeletedAt(Null)->orderBy('member_code', 'desc')->first()->member_code ?? 1;

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

        return view('admin.members.create', compact('statuses', 'sources', 'sales_bies', 'pricelists', 'trainers', 'last_member_code', 'last_invoice', 'addresses', 'accounts', 'memberStatuses', 'setting', 'sports', 'branches', 'selected_branch', 'main_schedules'));
    }

    public function store(Request $request)
    {
        $selected_branch = Auth()->user()->employee->branch ?? Null;

        $request->validate([
            'email' => 'nullable|unique:users,email',
            // 'member_code'           => 'unique:leads,member_code',
            'phone' => 'required_unless:minor,yes|min:11|max:11|unique:leads,phone',
            'national' => 'nullable|nullable|min:14|max:14|unique:leads,national',
            'name' => 'required',
            'status_id' => 'required',
            'source_id' => 'required',
            'address_id' => 'required',
            'dob' => 'required',
            'gender' => 'required',
            'sales_by_id' => 'required',
            'service_pricelist_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'invoice_id' => 'required',
            'membership_fee' => 'required',
            'discount' => 'required',
            'discount_amount' => 'required',
            'net_amount' => 'required',
            'received_amount' => 'required',
            'amount_pending' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $check_member_code = Lead::where('member_code', $request['member_code'])->where('branch_id', '=', $request['branch_id'])->get();
            if (count($check_member_code) > 0) {
                $this->duplicated_member_code();
                return back();
            }

            if (isset($request->minor)) {
                $phone = '0x' . random_int(100000000, 999999999);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => isset($request->email) && (!is_null($request->email)) ? $request->email : str_replace(' ', '_', $request->name) . $request->member_code . random_int(1, 9) . '@gmail.com',
                'password' => isset($request->minor) ? $phone : Hash::make($request->phone)
            ]);

            $member = Lead::create([
                'name'              => $request['name'],
                'type'              => 'member',
                'phone'             => isset($request->minor) ? $phone : $request['phone'],
                'member_code'       => $request['member_code'],
                'card_number'       => $request['card_number'],
                'national'          => $request['national'],
                'status_id'         => $request['status_id'],
                'source_id'         => $request['source_id'],
                'address_id'        => $request['address_id'],
                'dob'               => $request['dob'],
                'gender'            => $request['gender'],
                'sales_by_id'       => $request['sales_by_id'],
                'referral_member'   => $request['referral_member'],
                'address_details'   => $request['address_details'],
                'whatsapp_number'   => $request['whatsapp_number'],
                'notes'             => $request['notes'],
                'branch_id'         => $selected_branch->id,
                'created_by_id'     => Auth()->user()->id,
                'parent_phone'      => isset($request->minor) ? $request['parent_phone'] : null,
                'parent_details'    => isset($request->minor) ? $request['parent_details'] : null,
                'user_id'           => $user->id,
            ]);

            $membership = Membership::create([
                'start_date'            => $request->start_date,
                'end_date'              => $request->end_date,
                'member_id'             => $member->id,
                'trainer_id'            => $request->trainer_id,
                'service_pricelist_id'  => $request->service_pricelist_id,
                'sport_id'              => $request->sport_id ?? null,
                'notes'                 => $request->notes,
                'sales_by_id'           => $request->sales_by_id,
                'membership_status'     => 'new',
            ]);

            TrackMembership::create([
                'membership_id' => $membership->id,
                'status' => 'new'
            ]);

            $invoice = Invoice::create([
                'discount'              => $request->discount_amount,
                'discount_notes'        => $request->discount_notes,
                'service_fee'           => $request->membership_fee,
                'net_amount'            => $request->membership_fee - $request->discount_amount,
                'membership_id'         => $membership->id,
                'branch_id'             => $selected_branch->id,
                'sales_by_id'           => $request->sales_by_id,
                'status'                => ($request->membership_fee - $request->discount_amount) == $request->received_amount ? 'fullpayment' : 'partial',
                'created_by_id'         => Auth()->user()->id,
                'created_at'            => $request['created_at'] . date('H:i:s')
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
                        'sales_by_id' => $request->sales_by_id,
                        'created_by_id' => Auth()->user()->id,
                        'created_at' => $request['created_at'] . date('H:i:s'),
                        'notes' => $request['notes']
                    ]);

                    $payment->account->balance = $payment->account->balance + $payment->amount;
                    $payment->account->save();

                    $payment->transaction()->create([
                        'amount' => $account_amount,
                        'account_id' => $request->account_ids[$key],
                        'created_by' => Auth()->user()->id,
                        'created_at' => $request['created_at'] . date('H:i:s')
                    ]);
                }
            }

            $member->leadReminders()->delete();

            // Due payment reminder
            if ($invoice->status == 'partial') {
                Reminder::create([
                    'type' => 'due_payment',
                    'membership_id' => $membership->id,
                    'lead_id' => $member->id,
                    'due_date' => !is_null($request->due_date) ? $request->due_date : date('Y-m-d', strtotime('+3 Days')),
                    'user_id' => $request->sales_by_id,
                ]);

                $member->update([
                    'status_id' => Status::firstOrCreate(
                        ['name' => 'Debt Member'],
                        ['color' => 'warning', 'default_next_followup_days' => 1, 'need_followup' => 'yes']
                    )->id
                ]);
            } else {
                $member->update(['status_id' => $request->member_status_id]);
            }

            // Upgrade Reminder
            // Reminder::create([
            //     'type'              => 'upgrade',
            //     'membership_id'     => $membership->id,
            //     'lead_id'           => $member->id,
            //     'due_date'          => date('Y-m-d', strtotime($membership->start_date.'+'.$membership->service_pricelist->upgrade_date.'Days')),
            //     'user_id'           => $request->sales_by_id,
            // ]);

            // Follow up Reminder
            Reminder::create([
                'type' => 'follow_up',
                'membership_id' => $membership->id,
                'lead_id' => $member->id,
                'due_date' => date('Y-m-d', strtotime($membership->start_date . '+' . $membership->service_pricelist->followup_date . 'Days')),
                'user_id' => $request->sales_by_id,
            ]);

            // Renew Reminders
            $this->renew_call($membership);

            if ($request->input('photo', false)) {
                $member->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }

            if ($media = $request->input('ck-media', false)) {
                Media::whereIn('id', $media)->update(['model_id' => $member->id]);
            }

            DB::commit();
        } catch (\Exception $e) {
            dd($e->getMessage());
            $this->something_wrong();
            return back();
        }
        $this->created();
        return redirect()->route('admin.invoices.show', $invoice->id);
    }

    public function generateFakeEmail()
    {

        $microtime = microtime(true);
        $microtime = str_replace('.', '', $microtime);
        $email = substr($microtime, 0, 14) . "as@dotapps.net";
        $checkEmail = User::where('email', '=', $email)->get();
        if (count($checkEmail) > 0) {
            return $this->generateFakeEmail();
        }
        return $email;
    }

    public function generateFakeNID()
    {
        $microtime = microtime(true);
        $microtime = str_replace('.', '', $microtime);
        $national = substr($microtime, 0, 14);
        $checkNID = Lead::where('national', '=', $national)->get();
        if (count($checkNID) > 0) {
            return $this->generateFakeNID();
        }
        return $national;
    }

    public function edit(Lead $member)
    {
        abort_if(Gate::denies('member_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $statuses = Status::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $addresses = Address::pluck('name', 'id');

        $sources = Source::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sales_bies = User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->whereHas('employee', function ($i) {
            $i->whereStatus('active');
        })->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $member->load('status', 'source', 'sales_by');

        return view('admin.members.edit', compact('statuses', 'sources', 'sales_bies', 'member', 'addresses', 'branches'));
    }

    public function update(Request $request, Lead $member)
    {
        $request->validate([
            "email" => "nullable|unique:users,email,$member->user_id",
            "national" => "nullable|min:14|max:14|unique:leads,national,$member->id",
            "phone" => "required_unless:minor,yes|min:11|max:11|unique:leads,phone,$member->id",
            // 'member_code'       => "unique:leads,member_code,$member->member_code",
            'name' => 'required',
            'source_id' => 'required',
            'dob' => 'required',
            'gender' => 'required',
            'sales_by_id' => 'required',
        ]);

        $check_member_code = Lead::where('member_code', $request['member_code'])->where('branch_id', '=', $request['branch_id'])->get();
        if ($request['member_code'] != $member->member_code) {
            if ((count($check_member_code) > 0)) {
                $this->duplicated_member_code();
                return back();
            }
        }


        if (isset($request->minor)) {
            $phone = '0x' . random_int(100000000, 999999999);
        }

        if ($member->user) {
            $user = User::whereId($member->user_id)->update([
                'name' => $request->name,
                'email' => isset($request->email) && (!is_null($request->email)) ? $request->email : str_replace(' ', '_', $request->name) . $request->member_code . date('Y-m-d h:i:s') . '@gmail.com',
                'password' => Hash::make($request->phone)
            ]);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => isset($request->email) && (!is_null($request->email)) ? $request->email : str_replace(' ', '_', $request->name) . $request->member_code . '@gmail.com',
                'password' => Hash::make($request->phone)
            ]);

            $member->user_id = $user->id;
            $member->save();
        }

        // $member->update($request->all());
        $member->update([
            'name' => $request['name'],
            'phone' => isset($request->minor) ? $phone : $request['phone'],
            'member_code' => $request['member_code'],
            'card_number' => $request['card_number'],
            'national' => $request['national'],
            'status_id' => $request['status_id'],
            'source_id' => $request['source_id'],
            'address_id' => $request['address_id'],
            'branch_id' => $request['branch_id'],
            'dob' => $request['dob'],
            'gender' => $request['gender'],
            'sales_by_id' => $request['sales_by_id'],
            'referral_member' => $request['referral_member'],
            'address_details' => $request['address_details'],
            'whatsapp_number' => $request['whatsapp_number'],
            'notes' => $request['notes'],
            'parent_phone' => isset($request->minor) ? $request['parent_phone'] : null,
            'parent_details' => isset($request->minor) ? $request['parent_details'] : null,
            'created_at' => $request['created_at']
        ]);

        if (isset($request['retroactive']) && $member->has('memberships')) {
            foreach ($member->memberships as $key => $membership) {
                $membership->update([
                    'sales_by_id' => $request['sales_by_id']
                ]);

                $membership->invoice->update([
                    'sales_by_id' => $request['sales_by_id']
                ]);

                foreach ($membership->payments as $key => $payment) {
                    $payment->update([
                        'sales_by_id' => $request['sales_by_id']
                    ]);
                }
            }
        }


        if ($request->input('photo', false)) {
            if (!$member->photo || $request->input('photo') !== $member->photo->file_name) {
                if ($member->photo) {
                    $member->photo->delete();
                }
                $member->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }
        } elseif ($member->photo) {
            $member->photo->delete();
        }

        // if ($request->input('email')){
        //     $user = $member->user;
        //     if($user){
        //         $checkEmail = User::where('email',$request->input('email'))->get();
        //         if(count($checkEmail) == 0){
        //             $user->email = $request->input('email');
        //             $user->save();
        //         }
        //     }
        // }
        $this->updated();
        return redirect()->route('admin.members.index');
    }

    public function show(Lead $member)
    {
        abort_if(Gate::denies('member_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($member->type == 'lead') 
        {
            return redirect()->route('admin.leads.show', $member->id);
        }

        $member->load([
            'branch',
            'status',
            'source',
            'sales_by',
            'user',
            'invoices'              => fn($q) => $q->with(['membership.service_pricelist','sales_by','created_by'])
                            ->withSum('payments','amount')->latest(),
            'membership_attendances' => fn($q) => $q->with(['membership.service_pricelist'])->whereHas('membership',fn($q) => $q->where('status','!=','expired'))->latest(),
            'memberships'           => fn($q) => $q->with(['freezeRequests','assigned_coach'])->latest(),
            'messages'              => fn($q) => $q->latest(),
            'invitations'           => fn($q) => $q->latest(),
            'Notes'                 => fn($q) => $q->latest(),
            'memberRequests'        => fn($q) => $q->latest(),
            'freeSessions'          => fn($q) => $q->latest(),
            // 'leadReminders'         => fn($q) => $q->orderBy('due_date', 'desc'),
            'sales_reminders'       => fn($q) => $q->orderBy('due_date', 'desc'),
            'trainer_reminders'     => fn($q) => $q->orderBy('due_date', 'desc'),
            'reminderHistory'       => fn($q) => $q->latest(),
            'freezeRequests'        => fn($q) => $q->latest(),
            'membership_schedules'  => fn($q) => $q->with(['membership' => fn($y) => $y->withCount('trainer_attendances')])->latest(),
            'trainer_attendants'    => fn($q) => $q->latest(),
        ]);

        $status = Status::whereName('Block')->first();
        
        if ($member->status_id != NULL && $status && $status->id == $member->status_id) 
        {
            $member_blocked = true;
        } else {
            $member_blocked = false;
        }

        $main_membership = Membership::with([
            'member',
            'invitations',
            'freezeRequests' => fn($q) => $q
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('end_date', '>', date('Y-m-d'))
                ->whereStatus('confirmed')
                ->first()

        ])->whereMemberId($member->id)->whereIn('status', ['current', 'expiring'])
            ->with([
                'service_pricelist' => fn($q) => $q
                    ->with([
                        'service' => fn($x) => $x->with([
                            'service_type' => fn($i) => $i->whereMainService(true)
                        ])
                    ])
            ])->whereHas('service_pricelist', function ($q) {
                $q->whereHas('service', function ($x) {
                    $x->whereHas('service_type', function ($i) {
                        $i->whereMainService(true);
                    });
                });
            })
            ->withCount('invitations')
            ->first();

        $membership = Membership::with('member')
            ->whereMemberId($member->id)
            ->whereDate('end_date', '>=', date('Y-m-d'))
            ->first();

        $last_membership = Membership::with(['member', 'invitations'])
            ->whereHas('service_pricelist', function ($q) {
                $q->whereHas('service', function ($x) {
                    $x->whereHas('service_type', function ($i) {
                        $i->whereMainService(true);
                    });
                });
            })
            ->whereMemberId($member->id)
            ->orderBy('end_date', 'desc')
            ->withCount('invitations')
            ->first();

        $invoices = Invoice::withSum('payments', 'amount')
            ->whereHas('membership',fn($q) => $q->whereMemberId($member->id))
            ->latest()
            ->get();

        $invoices_without_refunds = Invoice::withSum('payments', 'amount')
            ->whereHas('membership',fn($q) => $q->whereMemberId($member->id))
            ->where('status', '!=', 'refunded')
            ->latest()
            ->get();


        $trainers = User::whereRelation('roles','title','Trainer')
                        ->whereHas(
                            'employee',fn($q) => $q->whereStatus('active')->when(Auth()->user()->employee && Auth()->user()->employee->branch_id != NULL,fn($q) => $q->whereBranchId(Auth()->user()->employee->branch->id))
                        )
                        ->orderBy('name')
                        ->pluck('name', 'id');

        return view('admin.members.show',compact('member','member_blocked','trainers','main_membership','membership','last_membership','invoices','invoices_without_refunds'));
    }

    public function show_old(Lead $member)
    {
        abort_if(Gate::denies('member_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($member->type == 'lead') 
        {
            return redirect()->route('admin.leads.show', $member->id);
        }

        $member->loadCount('freeSessions')->load([
            'status',
            'source',
            'sales_by',
            'user',
            'memberships'           => fn($q) => $q->with(['freezeRequests','assigned_coach'])->latest(),
            'messages'              => fn($q) => $q->latest(),
            'invitations'           => fn($q) => $q->latest(),
            'Notes'                 => fn($q) => $q->latest(),
            'memberRequests'        => fn($q) => $q->latest(),
            'freeSessions'          => fn($q) => $q->latest(),
            // 'leadReminders'         => fn($q) => $q->orderBy('due_date', 'desc'),
            'sales_reminders'       => fn($q) => $q->orderBy('due_date', 'desc'),
            'trainer_reminders'     => fn($q) => $q->orderBy('due_date', 'desc'),
            'reminderHistory'       => fn($q) => $q->latest(),
            'freezeRequests'        => fn($q) => $q->latest(),
            'membership_schedules'  => fn($q) => $q->with(['membership' => fn($y) => $y->withCount('trainer_attendances')])->latest(),
            'trainer_attendants'    => fn($q) => $q->latest(),
        ]);
        // $birthday = $member->whereMonth('dob',date('m'))->whereDay('dob',date('d'))->first()->dob;

        $attendances = MembershipAttendance::whereIn('membership_id', $member->memberships()
            ->get('id')
            ->toArray())
            ->latest()
            ->get();


        $invoices = Invoice::withSum('payments', 'amount')
            ->whereIn('membership_id', $member->memberships()
                ->get('id')
                ->toArray())
            ->latest()
            ->get();

        $invoices_without_refunds = Invoice::withSum('payments', 'amount')
            ->whereIn('membership_id', $member->memberships()
                ->where('status', '!=', 'refunded')
                ->get('id')
                ->toArray())
            ->latest()
            ->get();

        $membership = Membership::with('member')
            ->whereMemberId($member->id)
            ->whereDate('end_date', '>=', date('Y-m-d'))
            ->first();

        $main_membership = Membership::with([
            'member',
            'invitations',
            'freezeRequests' => fn($q) => $q
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('end_date', '>', date('Y-m-d'))
                ->whereStatus('confirmed')
                ->first()

        ])->whereMemberId($member->id)->whereIn('status', ['current', 'expiring'])
            ->with([
                'service_pricelist' => fn($q) => $q
                    ->with([
                        'service' => fn($x) => $x->with([
                            'service_type' => fn($i) => $i->whereMainService(true)
                        ])
                    ])
            ])->whereHas('service_pricelist', function ($q) {
                $q->whereHas('service', function ($x) {
                    $x->whereHas('service_type', function ($i) {
                        $i->whereMainService(true);
                    });
                });
            })
            ->withCount('invitations')
            ->first();


        $last_membership = Membership::with(['member', 'invitations'])
            ->whereHas('service_pricelist', function ($q) {
                $q->whereHas('service', function ($x) {
                    $x->whereHas('service_type', function ($i) {
                        $i->whereMainService(true);
                    });
                });
            })
            ->whereMemberId($member->id)
            ->orderBy('end_date', 'desc')
            ->withCount('invitations')
            ->first();
        if (!$last_membership) {
            Membership::with(['member', 'invitations'])
                ->whereMemberId($member->id)
                ->orderBy('end_date', 'desc')
                ->withCount('invitations')
                ->first();
        }
        $total_attendances = 0;
        foreach ($member->memberships as $membership) {
            $total_attendances += $membership->attendances->count();
        }

        $statuses = Status::pluck('name', 'id');

        $memberships = Membership::whereMemberId($member->id)->get();

        foreach ($memberships as $key => $membership) {
            $this->adjustMembership($membership);
        }

        $status = Status::whereName('Block')->first();

        if ($member->status_id != NULL && $status && $status->id == $member->status_id) {
            $member_blocked = true;
        } else {
            $member_blocked = false;
        }


        foreach ($member->invoices as $key => $invoice) {
            if ($invoice->status != 'refunded' && $invoice->status != 'settlement') {
                $invoice->net_amount    = $invoice->service_fee - $invoice->discount;
                $invoice->status        = $invoice->payments->sum('amount') == ($invoice->service_fee - $invoice->discount) ? 'fullpayment' : 'partial';
                $invoice->save();
            }
        }

        $trainers = User::whereRelation('roles','title','Trainer')
                        ->whereHas(
                            'employee',fn($q) => $q->whereStatus('active')->when(Auth()->user()->employee && Auth()->user()->employee->branch_id != NULL,fn($q) => $q->whereBranchId(Auth()->user()->employee->branch->id))
                        )
                        ->orderBy('name')
                        ->pluck('name', 'id');

        return view('admin.members.show', compact('member', 'attendances', 'invoices', 'invoices_without_refunds', 'membership', 'main_membership', 'last_membership', 'total_attendances', 'statuses', 'member_blocked','trainers'));
    }

    public function destroy(Lead $member)
    {
        abort_if(Gate::denies('member_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $member->load(['invoices', 'leadReminders']);
        foreach ($member->invoices as $key => $invoice) {
            foreach ($invoice->payments as $k => $payment) {
                $payment->account->balance -= $payment->amount;
                $payment->account->save();
            }
        }
        // $member->leadReminders()->delete();
        $member->delete();

        $this->deleted();
        return back();
    }

    public function massDestroy(MassDestroyLeadRequest $request)
    {
        $members = Lead::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('member_create') && Gate::denies('member_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new Lead();
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function transfer($id)
    {
        $selected_branch = isset(Auth()->user()->employee) ? Auth()->user()->employee->branch : Null;

        $lead = Lead::findOrFail($id);

        $statuses = Status::pluck('name', 'id');

        $sources = Source::pluck('name', 'id');

        $addresses = Address::pluck('name', 'id');

        $sales_bies = User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->whereHas('employee', function ($i) use ($selected_branch) {
            $i->whereStatus('active') ->when($selected_branch, function ($q) use ($selected_branch) {
                $q->whereBranchId($selected_branch->id);
            });
        })->pluck('name', 'id');

        $pricelists = Pricelist::whereStatus('active')->with(['service'])->latest()->get();

        $trainers = User::whereHas('roles', function ($q) {
            $q->where('title', 'Trainer');
        })->whereHas('employee', function ($i) use ($selected_branch) {
            $i->whereStatus('active') ->when($selected_branch, function ($q) use ($selected_branch) {
                $q->whereBranchId($selected_branch->id);
            });
        })->pluck('name', 'id');

        $last_member_code = Lead::whereType('member') ->when($selected_branch, function ($q) use ($selected_branch) {
            $q->whereBranchId($selected_branch->id);
        })->whereDeletedAt(Null)->orderBy('member_code', 'desc')->first()->member_code ?? 1;

        // $last_member_code = Lead::whereType('member')->whereDeletedAt(Null)->orderBy('member_code','desc')->first()->member_code ?? 1;
        $last_invoice = Invoice::latest()->first()->id ?? 0;

        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $accounts = Account::pluck('name', 'id');

        $memberStatuses = MemberStatus::pluck('name', 'id');

        $setting = Setting::first();

        // $selected_branch = Auth()->user()->employee->branch ?? NULL;


        $sports = Sport::pluck('name', 'id');

        $main_schedules = ScheduleMain::with(['session', 'trainer'])
            ->whereStatus('active')
            ->when($selected_branch, function ($q) use ($selected_branch) {
                $q->whereBranchId($selected_branch->id);
            })
            ->whereHas('schedule_main_group', fn($q) => $q->whereStatus('active'))
            ->latest()
            ->get();

        return view('admin.members.transfer', compact('lead', 'last_member_code', 'last_invoice', 'statuses', 'sources', 'addresses', 'sales_bies', 'pricelists', 'trainers', 'accounts', 'memberStatuses', 'setting', 'sports', 'branches', 'selected_branch', 'main_schedules'));
    }

    public function storeTransfer($id, Request $request)
    {
        $member = Lead::findOrFail($id);

        $sales_by_id = $member->sales_by_id;

        $request->validate([
            "email" => "nullable|unique:users,email,$member->user_id",
            "national" => "nullable|min:14|max:14|unique:leads,national,$id",
            "phone" => "required|min:11|max:11|unique:leads,phone,$id",
            'name' => 'required',
            // 'member_code'           => 'required|unique:leads,member_code',
            'status_id' => 'required',
            'source_id' => 'required',
            'address_id' => 'required|integer',
            'dob' => 'required',
            'gender' => 'required',
            'sales_by_id' => 'required',
            'service_pricelist_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'invoice_id' => 'required',
            'membership_fee' => 'required',
            'discount' => 'required',
            'discount_amount' => 'required',
            'net_amount' => 'required',
            'received_amount' => 'required',
            'amount_pending' => 'required',
        ]);

        $check_member_code = Lead::where('member_code', $request['member_code'])->where('branch_id', '=', $request['branch_id'])->get();
        if (count($check_member_code) > 0) {
            $this->duplicated_member_code();
            return back();
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => isset($request->email) && (!is_null($request->email)) ? $request->email : str_replace(' ', '_', $request->name) . $request->member_code . date('Y-m-d h:i:s') . '@gmail.com',
                'password' => Hash::make($request->phone)
            ]);

            $member->update([
                'type' => 'member',
                'name' => $request['name'],
                'phone' => $request['phone'],
                'national' => $request['national'],
                'member_code' => $request['member_code'],
                'status_id' => $request['status_id'],
                'source_id' => $request['source_id'],
                'address_id' => $request['address_id'],
                'branch_id' => $request['branch_id'],
                'card_number' => $request['card_number'],
                'dob' => $request['dob'],
                'gender' => $request['gender'],
                'notes' => $request['notes'],
                'user_id' => $user->id,
                'sales_by_id' => $sales_by_id,
                'created_by_id' => $member->created_by_id == NULL ? Auth()->user()->id : $member->created_by_id
            ]);

            $membership = Membership::create([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'member_id' => $member->id,
                'trainer_id' => $request->trainer_id,
                'service_pricelist_id' => $request->service_pricelist_id,
                'sales_by_id' => $sales_by_id,
                'notes' => $request->notes,
                'created_at' => $request['created_at'] . date('H:i:s'),
                'membership_status' => 'new',
                'sport_id' => $request->sport_id ?? null
            ]);

            $member->leadReminder()->delete();

            // Renew Reminders
            $this->renew_call($membership);

            $invoice = Invoice::create([
                'discount' => $request->discount_amount,
                'discount_notes' => $request->discount_notes,
                'service_fee' => $request->membership_fee,
                'net_amount' => $request->membership_fee - $request->discount_amount,
                'membership_id' => $membership->id,
                'sales_by_id' => $sales_by_id,
                'status' => ($request->membership_fee - $request->discount_amount) == $request->received_amount ? 'fullpayment' : 'partial',
                'created_by_id' => Auth()->user()->id,
                'created_at' => $request['created_at'] . date('H:i:s'),
                'branch_id' => $request['branch_id']
            ]);

            foreach ($request['account_amount'] as $key => $account_amount) {
                if ($account_amount > 0) {
                    $payment = Payment::create([
                        'account_id' => $request->account_ids[$key],
                        'amount' => $account_amount,
                        'invoice_id' => $invoice->id,
                        'sales_by_id' => $sales_by_id,
                        'created_by_id' => Auth()->user()->id,
                        'created_at' => $request['created_at'] . date('H:i:s'),
                        'notes' => $request['notes']
                    ]);

                    $payment->account->balance = $payment->account->balance + $payment->amount;
                    $payment->account->save();

                    $transaction = Transaction::create([
                        'transactionable_type' => 'App\\Models\\Payment',
                        'transactionable_id' => $payment->id,
                        'amount' => $account_amount,
                        'account_id' => $request->account_ids[$key],
                        'created_by' => Auth()->user()->id,
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
                // Due payment reminder
                Reminder::create([
                    'type' => 'due_payment',
                    'membership_id' => $membership->id,
                    'lead_id' => $member->id,
                    'due_date' => $request->due_date != NULL ? $request->due_date : date('Y-m-d', strtotime('+3 Days')),
                    'user_id' => $sales_by_id,
                ]);

                $member->update([
                    'status_id' => Status::firstOrCreate(
                        ['name' => 'Debt Member'],
                        ['color' => 'warning', 'default_next_followup_days' => 1, 'need_followup' => 'yes']
                    )->id
                ]);
            } else {
                $member->update(['status_id' => $request->member_status_id]);
            }

            // Upgrade Reminder
            // Reminder::create([
            //     'type'              => 'upgrade',
            //     'membership_id'     => $membership->id,
            //     'lead_id'           => $member->id,
            //     'due_date'          => date('Y-m-d', strtotime($membership->start_date.'+'.$membership->service_pricelist->upgrade_date.' Days')),
            //     'user_id'           => $sales_by_id,
            // ]);

            // Follow up Reminder
            Reminder::create([
                'type' => 'follow_up',
                'membership_id' => $membership->id,
                'lead_id' => $member->id,
                'due_date' => date('Y-m-d', strtotime($membership->start_date . '+' . $membership->service_pricelist->followup_date . ' Days')),
                'user_id' => $sales_by_id,
            ]);

            if ($request->input('photo', false)) {
                $member->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }

            if ($media = $request->input('ck-media', false)) {
                Media::whereIn('id', $media)->update(['model_id' => $member->id]);
            }

            if ($request->input('photo', false)) {
                if (!$member->photo || $request->input('photo') !== $member->photo->file_name) {
                    if ($member->photo) {
                        $member->photo->delete();
                    }
                    $member->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
                }
            } elseif ($member->photo) {
                $member->photo->delete();
            }
            DB::commit();
        } catch (\Exception $e) {
            dd($e);
            $this->something_wrong();
            return back();
        }

        $this->transfered();

        return redirect()->route('admin.invoices.show', $invoice->id);
    }

    public function addMembership($id)
    {
        $member = Lead::whereType('member')->with(['memberships'])->findOrFail($id);

        $sales_bies = User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->whereHas('employee', function ($i) {
            $i->whereStatus('active');
        })->pluck('name', 'id');

        $pricelists = Pricelist::whereStatus('active')->with(['service'])->latest()->get();

        $trainers = User::whereHas('roles', function ($q) {
            $q->where('title', 'Trainer');
        })->whereHas('employee', function ($i) {
            $i->whereStatus('active');
        })->pluck('name', 'id');

        $last_invoice = Invoice::latest()->first()->id ?? 0;

        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $selected_branch = isset(Auth()->user()->employee) ? Auth()->user()->employee->branch : Null;

        $accounts = Account::pluck('name', 'id');

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

        return view('admin.members.add_membership', compact('member', 'sales_bies', 'pricelists', 'trainers', 'last_invoice', 'accounts', 'memberStatuses', 'setting', 'sports', 'branches', 'selected_branch', 'main_schedules'));
    }

    public function addNewMembership(Request $request, $id)
    {
        $request->validate([
            'service_pricelist_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'invoice_id' => 'required',
            'membership_fee' => 'required',
            'discount' => 'required',
            'discount_amount' => 'required',
            'net_amount' => 'required',
            'received_amount' => 'required',
            'amount_pending' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $member = Lead::whereType('member')->with('memberships')->findOrFail($id);

            // $old_memberships = $member->memberships->toArray();

            // $new_pricelist = Pricelist::with(['service','service.service_type'])
            //                             ->find($request['service_pricelist_id'])
            //                             ->service->service_type;

            $membership = Membership::create([
                'start_date' => $request['start_date'],
                'end_date' => $request['end_date'],
                'member_id' => $member->id,
                'trainer_id' => $request['trainer_id'],
                'service_pricelist_id' => $request['service_pricelist_id'],
                'sales_by_id' => $member->sales_by_id,
                'notes' => $request['subscription_notes'],
                'created_at' => $request['created_at'] . date('H:i:s'),
                'membership_status' => 'renew'
            ]);

            $invoice = Invoice::create([
                'discount' => $request->discount_amount,
                'discount_notes' => $request->discount_notes,
                'service_fee' => $request->membership_fee,
                'net_amount' => $request->membership_fee - $request->discount_amount,
                'membership_id' => $membership->id,
                'branch_id' => $request['branch_id'],
                'sales_by_id' => $member->sales_by_id,
                'status' => ($request->membership_fee - $request->discount_amount) == $request->received_amount ? 'fullpayment' : 'partial',
                'created_by_id' => Auth()->user()->id,
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
                        'sales_by_id' => $member->sales_by_id,
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

            // Upgrade Reminder
            //  Reminder::create([
            //     'type'              => 'upgrade',
            //     'membership_id'     => $membership->id,
            //     'lead_id'           => $member->id,
            //     'due_date'          => date('Y-m-d', strtotime($membership->start_date.'+'.$membership->service_pricelist->upgrade_date.'Days')),
            //     'user_id'           => $member->sales_by_id,
            // ]);

            // Follow up Reminder
            Reminder::create([
                'type' => 'follow_up',
                'membership_id' => $membership->id,
                'lead_id' => $member->id,
                'due_date' => date('Y-m-d', strtotime($membership->start_date . '+' . $membership->service_pricelist->followup_date . 'Days')),
                'user_id' => $member->sales_by_id,
            ]);

            if ($invoice->status == 'partial') {
                Reminder::create([
                    'type' => 'due_payment',
                    'membership_id' => $membership->id,
                    'lead_id' => $member->id,
                    'due_date' => $request->due_date != NULL ? $request->due_date : date('Y-m-d', strtotime('+3 Days')),
                    'user_id' => $member->sales_by_id,
                ]);

                $member->update([
                    'status_id' => Status::firstOrCreate(
                        ['name' => 'Debt Member'],
                        ['color' => 'warning', 'default_next_followup_days' => 1, 'need_followup' => 'yes']
                    )->id
                ]);
            } else {
                $member->update(['status_id' => $request->member_status_id]);
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

    function sendMessage(Request $request, $id)
    {
        $member = Lead::findOrFail($id);;
        $twilio_data = json_decode(Marketing::where('service', 'sms')->first()->settings);

        try {

            $client = new Client($twilio_data->account_sid, $twilio_data->auth_token);
            $client->messages->create('+2' . $member->phone, [
                'from' => $twilio_data->phone,
                'body' => $request->message
            ]);
            $twilio_data->numbers = $member->phone;

            Sms::create([
                'sent_by' => $member->sales_by_id,
                'message' => $request->message,
                'numbers' => $member->phone,
            ]);

            $this->created();
        } catch (\Exception $ex) {
            dd($ex->getMessage());
        }

        return back();
    }

    public function editCardNumber($id)
    {
        $member = Lead::whereType('member')->findOrFail($id);

        return view('admin.members.editCardNumber', compact('member'));
    }

    public function updateCardNumber(Request $request, $id)
    {
        $member = Lead::whereType('member')->findOrFail($id);
        $member->update([
            'card_number' => $request['card_number']
        ]);

        if ($request->input('photo', false)) {
            if (!$member->photo || $request->input('photo') !== $member->photo->file_name) {
                if ($member->photo) {
                    $member->photo->delete();
                }
                $member->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }
        } elseif ($member->photo) {
            $member->photo->delete();
        }

        $this->updated();
        return redirect()->route('admin.members.index');
    }

    public function export(Request $request)
    {
        return Excel::download(new MembersExport($request), 'Members.xlsx');
    }

    public function invitations(Request $request)
    {
        $invitations = Invitation::index($request->all())
            ->with(['lead', 'member', 'membership'])
            ->latest()
            ->get();

        return view('invitations.index', compact('invitations'));
    }

    public function exportInvitations(Request $request)
    {
        return Excel::download(new InvitationsExport($request), 'Invitations.xlsx');
    }

    public function activeMembers(Request $request)
    {
        $setting = Setting::first()->inactive_members_days ?? 7;

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $memberships = Membership::with([
            'member',
            'service_pricelist',
            'service_pricelist.service',
            'invoice',
            'member.branch'
        ])
            ->whereHas('member', fn($q) => $q->when($branch_id, fn($y) => $y->whereBranchId($branch_id)))
            // ->whereHas('attendances')
            ->whereDate('last_attendance', '>=', date('Y-m-d', strtotime('-' . $setting . 'Days')))
            ->whereIn('status', ['current', 'expiring'])
            ->whereHas('service_pricelist', function ($y) {
                $y->whereHas('service', function ($b) {
                    $b->whereHas('service_type', function ($x) {
                        $x->whereMainService(true);
                    });
                });
            })
            ->latest()
            ->get();

        return view('admin.members.active', compact('memberships', 'employee', 'branch_id'));
    }

    public function onHoldMembers(Request $request)
    {
        $setting = Setting::first()->inactive_members_days ?? 7;

        $sport_id = $request['sport_id'] ?? NULL;

        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $memberships = Membership::with([
            'member',
            'member.sport',
            'service_pricelist',
            'service_pricelist.service',
            'invoice' => fn($q) => $q->withSum('payments', 'amount'),
            'member.branch',
            'sales_by'
        ])
            ->whereDate('last_attendance', '<', date('Y-m-d', strtotime('-' . $setting . 'Days')))
            ->whereHas('member', fn($q) => $q->when($sport_id, fn($q) => $q->whereSportId($sport_id))->when($branch_id, fn($y) => $y->whereBranchId($branch_id)))
            // ->whereHas('attendances')
            // ->orWhere('last_attendance',NULL)
            ->whereIn('status', ['current', 'expiring'])
            ->whereHas('service_pricelist', function ($y) {
                $y->whereHas('service', function ($b) {
                    $b->whereHas('service_type', function ($x) {
                        $x->whereMainService(true);
                    });
                });
            })
            ->latest()
            ->get();

        $statuses = Status::pluck('name', 'id');

        return view('admin.members.onhold', compact('memberships', 'statuses', 'employee', 'branch_id'));
    }

    public function inactiveMembers(Request $request)
    {
        $employee = Auth()->user()->employee;

        if ($employee && $employee->branch_id != NULL) {
            $branch_id = $employee->branch_id;
        } else {
            $branch_id = $request['branch_id'] != NULL ? $request['branch_id'] : '';
        }

        $sport_id = $request['sport_id'] ?? NULL;

        $members = Lead::whereType('member')
            ->whereDoesntHave('memberships', fn($q) => $q->whereIn('status', ['expiring', 'current']))
            ->with(['memberships', 'branch', 'sport'])
            ->when($branch_id, fn($q) => $q->whereBranchId($branch_id))
            ->when($sport_id, fn($q) => $q->whereSportId($sport_id))
            ->withCount(['memberships'])
            ->get();

        return view('admin.members.inactive', compact('members', 'employee', 'branch_id'));
    }

    public function createPopMessage($id)
    {
        $member = Lead::whereType('member')->findOrFail($id);

        return view('admin.pop_messages.create', compact('member'));
    }

    public function storePopMessage(Request $request, $id)
    {
        $member = Lead::whereType('member')->findOrFail($id);
        PopMessage::create([
            'message' => $request['message'],
            'member_id' => $member->id,
            'created_by_id' => Auth()->user()->id,
        ]);

        $this->sent_successfully();
        return redirect()->route('admin.members.show', $member->id);
    }

    public function showPopMessage($id)
    {
        $pop_message = PopMessage::findOrFail($id);

        return view('admin.pop_messages.reply', compact('pop_message'));
    }

    public function destroyPopMessage($id)
    {
        PopMessage::findOrFail($id)->delete();

        $this->deleted();
        return back();
    }

    public function storePopMessageReply(Request $request, $id)
    {
        $pop_message = PopMessage::findOrFail($id);

        PopMessageReply::create([
            'reply' => $request['reply'],
            'pop_message_id' => $pop_message->id,
            'created_by_id' => Auth()->user()->id,
        ]);

        $this->sent_successfully();
        return back();
    }

    public function exportActiveMembers(Request $request)
    {
        return Excel::download(new ActiveMembersExport($request), 'Active-Members.xlsx');
    }

    public function exportOnhold(Request $request)
    {
        return Excel::download(new OnholdExport($request), 'On-hold-Members.xlsx');
    }

    public function block(Lead $member)
    {
        $member->load(['status']);
        $member->update([
            'status_id' => Status::whereName('Block')->first()->id ?? Status::create(['name' => 'Block', 'color' => 'red', 'need_followup' => 'no', 'default_next_followup_days' => 0])
        ]);

        $this->sent_successfully();
        return back();
    }

    public function transfer_to_branch(Lead $member)
    {
        return response()->json($member);    
    }

    public function store_transfer_to_branch(Request $request,Lead $member)
    {
        $member->update([
            'branch_id'     => $request['branch_id']
        ]);

        $this->sent_successfully();
        return back();
    }
}
