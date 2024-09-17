<?php

namespace App\Http\Controllers\Admin;

use PDF;
use I18N_Arabic;
use App\Models\User;
use App\Models\Refund;
use App\Models\Status;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Reminder;
use App\Models\Pricelist;
use App\Models\Membership;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\MemberStatus;
use App\Models\RefundReason;
use Illuminate\Http\Request;
use App\Models\MemberReminder;
use App\Exports\InvoicesExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PartialInvoicesExport;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\Party;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyInvoiceRequest;
use App\Models\Branch;
use LaravelDaily\Invoices\Invoice as LaravelInvoices;

class InvoiceController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {

        abort_if(Gate::denies('invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $invoices = Invoice::where('status','!=','refund')->get();
        // foreach($invoices as $invoice){
        //     $rest =  $invoice->net_amount - $invoice->payments->sum('amount');
        //     if($rest > 0){
        //         $invoice->status = 'partial';
        //         $invoice->save();
        //     }else{
        //         $invoice->status = 'fullpayment';
        //         $invoice->save();
        //     }

        // }
        // dd('success');
        // return $request->all();
        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);

        $setting = Setting::first();

        $employee = Auth()->user()->employee;

        if ($request->ajax()) {
            if ($employee && $employee->branch_id != NULL) {
                $query = Invoice::index($data)
                    ->with([
                        'membership',
                        'membership.service_pricelist',
                        'membership.member',
                        'sales_by',
                        'payments',
                        'created_by',
                    ])
                    ->whereBranchId($employee->branch_id)
                    ->whereHas('membership')
                    ->withSum('payments', 'amount')
                    ->select(sprintf('%s.*', (new Invoice())->table));
            } else {
                $query = Invoice::index($data)
                    ->with([
                        'membership',
                        'membership.service_pricelist',
                        'membership.member',
                        'sales_by',
                        'payments',
                        'created_by',
                    ])
                    // ->whereStatus('settlement')
                    ->whereHas('membership')
                    ->withSum('payments', 'amount')
                    ->select(sprintf('%s.*', (new Invoice())->table));
            }

            $table = Datatables::eloquent($query);

            $table->addColumn('actions', '&nbsp;');
            $table->addColumn('placeholder', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'invoice_show';
                $editGate = 'invoice_edit';
                $deleteGate = 'invoice_delete';
                $crudRoutePart = 'invoices';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {

                return $row->id ? $row->id : '';
            });

            $table->editColumn('discount', function ($row) {
                return $row->discount ? $row->discount . ' EGP' . '<br />' . ($row->discount_notes !== NULL ? $row->discount_notes : '<span class="badge badge-danger">No Notes</span>') :  '0 EGP' . '<br />' . ($row->discount_notes !== NULL ? $row->discount_notes : '<span class="badge badge-danger">No Notes</span>');
            });

            $table->addColumn('member', function ($row) use ($setting) {
                return $row->membership && $row->membership->member ? '<a href="' . route('admin.members.show', $row->membership->member_id) . '" target="_blank">' . $row->membership->member->branch->member_prefix . $row->membership->member->member_code . ' <br> ' . $row->membership->member->name . '<br>' . $row->membership->member->phone . '</a>' : '';
            });

            $table->editColumn('amount', function ($row) {
                return $row->net_amount ? "<span class='text-success font-weight-bold'>" . trans('global.net') . "</span>" . ' : ' . $row->net_amount . ' EGP' . '<br/>' .  "<span class='text-primary font-weight-bold'>" . trans('invoices::invoice.paid') . "</span>" . ' : ' . $row->payments->sum('amount') . ' EGP <br />' . "<span class='text-danger font-weight-bold'>" . trans('global.rest') . "</span>" . ' : ' . $row->rest . ' EGP' : '';
            });

            $table->editColumn('status', function ($row) {
                return '<span class="badge badge-' . Invoice::STATUS_COLOR[$row->status] . '  p-2">' . Invoice::STATUS_SELECT[$row->status] . '</span>';
            });

            if (config('domains')[config('app.url')]['is_reviewed_invoices'] == true) {
                $table->editColumn('review_status', function ($row) {
                    if ($row->is_reviewed == 0) {
                        return "<span class='badge badge-warning px-2 py-2'>" . trans('global.not_reviewed') . "</span>";
                    } else {
                        return "<span class='badge badge-success px-2 py-2'>" . trans('global.is_reviewed') . "</span>";
                    }
                });
            }

            $table->addColumn('membership_service', function ($row) {
                return $row->membership && $row->membership->service_pricelist && $row->membership->service_pricelist->service && $row->membership->service_pricelist->service->service_type ? $row->membership->service_pricelist->name . '<br />' . trans('cruds.invoice.fields.service_fee') . ' : ' . $row->service_fee . ' EGP' . '<br>' . '<span class="badge p-2 badge-' . Membership::MEMBERSHIP_STATUS_COLOR[$row->membership->membership_status] . '"">' . Membership::MEMBERSHIP_STATUS[$row->membership->membership_status] . '</span>' : '-';
            });

            $table->addColumn('trainer', function ($row) {
                return $row->membership && $row->membership->trainer ? $row->membership->trainer->name : '-';
            });

            $table->addColumn('member_code_number', function ($row) {
                return $row->membership && $row->membership->member ? $row->membership->member->member_code : $row->id;
            });

            $table->addColumn('sales_by_name', function ($row) {
                return $row->sales_by ? $row->sales_by->name : '';
            });

            $table->addColumn('branch_name', function ($row) {
                return $row->membership->member && $row->membership->member->branch ? $row->membership->member->branch->name : '-';
            });

            $table->addColumn('created_by', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });


            $table->rawColumns(['actions', 'placeholder', 'amount', 'membership', 'sales_by', 'invoice_paid', 'status', 'membership_service', 'member', 'created_by', 'trainer', 'review_status', 'discount', 'member_code_number', 'branch_name']);

            return $table->make(true);
        }

        $sales_bies = User::whereHas('roles', function ($q) {
            $q = $q->whereTitle('Sales');
        })->pluck('name', 'id');

        $branches = Branch::pluck('name', 'id');

        if ($employee && $employee->branch_id != NULL) {
            $db_invoices = Invoice::index($data)->whereBranchId($employee->branch_id)->where('status', '!=', 'refund');

            $payments = Payment::whereHas('invoice', function ($q) use ($employee) {
                $q->where('status', '!=', 'refund')->whereBranchId($employee->branch_id);
            })->when(count($data) > 0, function ($q) use ($db_invoices) {
                $q->whereIn('invoice_id', $db_invoices->pluck('id')->toArray());
            })->get();
        } else {
            $db_invoices = Invoice::index($data)->where('status', '!=', 'refund');

            $payments = Payment::whereHas('invoice', function ($q) {
                $q->where('status', '!=', 'refund');
            })->when(count($data) > 0, function ($q) use ($db_invoices) {
                $q->whereIn('invoice_id', $db_invoices->pluck('id')->toArray());
            })->get();
        }



        return view('admin.invoices.index', [
            'sales_bies' => $sales_bies,
            'payments' => $payments,
            'invoices' => $db_invoices,
            'branches' => $branches,
        ]);
    }
    public function settled(Request $request)
    {

        abort_if(Gate::denies('invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);

        $setting = Setting::first();

        $employee = Auth()->user()->employee;

        if ($request->ajax()) {
            if ($employee && $employee->branch_id != NULL) {
                $query = Invoice::index($data)
                    ->with([
                        'membership',
                        'membership.service_pricelist',
                        'membership.member',
                        'sales_by',
                        'payments',
                        'created_by',
                    ])
                    ->whereBranchId($employee->branch_id)
                    ->whereHas('membership')
                    ->withSum('payments', 'amount')
                    ->whereStatus('settlement')
                    ->select(sprintf('%s.*', (new Invoice())->table));
            } else {
                $query = Invoice::index($data)
                    ->with([
                        'membership',
                        'membership.service_pricelist',
                        'membership.member',
                        'sales_by',
                        'payments',
                        'created_by',
                    ])
                    ->whereHas('membership')
                    ->withSum('payments', 'amount')
                    ->whereStatus('settlement')
                    ->select(sprintf('%s.*', (new Invoice())->table));
            }

            $table = Datatables::eloquent($query);

            $table->addColumn('actions', '&nbsp;');
            $table->addColumn('placeholder', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'invoice_show';
                $editGate = 'invoice_edit';
                $deleteGate = 'invoice_delete';
                $crudRoutePart = 'invoices.settled';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {

                return $row->id ? $row->id : '';
            });

            $table->editColumn('discount', function ($row) {
                return $row->discount ? $row->discount . ' EGP' . '<br />' . ($row->discount_notes !== NULL ? $row->discount_notes : '<span class="badge badge-danger">No Notes</span>') :  '0 EGP' . '<br />' . ($row->discount_notes !== NULL ? $row->discount_notes : '<span class="badge badge-danger">No Notes</span>');
            });

            $table->addColumn('member', function ($row) use ($setting) {
                return $row->membership && $row->membership->member ? '<a href="' . route('admin.members.show', $row->membership->member_id) . '" target="_blank">' . $row->membership->member->branch->member_prefix . $row->membership->member->member_code . ' <br> ' . $row->membership->member->name . '<br>' . $row->membership->member->phone . '</a>' : '';
            });

            $table->editColumn('amount', function ($row) {
                return $row->net_amount ? "<span class='text-success font-weight-bold'>" . trans('global.net') . "</span>" . ' : ' . $row->net_amount . ' EGP' . '<br/>' .  "<span class='text-primary font-weight-bold'>" . trans('invoices::invoice.paid') . "</span>" . ' : ' . $row->payments->sum('amount') . ' EGP <br />' . "<span class='text-danger font-weight-bold'>" . trans('global.rest') . "</span>" . ' : ' . $row->rest . ' EGP' : '';
            });

            $table->editColumn('status', function ($row) {
                return '<span class="badge badge-' . Invoice::STATUS_COLOR[$row->status] . '  p-2">' . Invoice::STATUS_SELECT[$row->status] . '</span>';
            });

            if (config('domains')[config('app.url')]['is_reviewed_invoices'] == true) {
                $table->editColumn('review_status', function ($row) {
                    if ($row->is_reviewed == 0) {
                        return "<span class='badge badge-warning px-2 py-2'>" . trans('global.not_reviewed') . "</span>";
                    } else {
                        return "<span class='badge badge-success px-2 py-2'>" . trans('global.is_reviewed') . "</span>";
                    }
                });
            }

            $table->addColumn('membership_service', function ($row) {
                return $row->membership && $row->membership->service_pricelist && $row->membership->service_pricelist->service && $row->membership->service_pricelist->service->service_type ? $row->membership->service_pricelist->name . '<br />' . trans('cruds.invoice.fields.service_fee') . ' : ' . $row->service_fee . ' EGP' . '<br>' . '<span class="badge p-2 badge-' . Membership::MEMBERSHIP_STATUS_COLOR[$row->membership->membership_status] . '"">' . Membership::MEMBERSHIP_STATUS[$row->membership->membership_status] . '</span>' : '-';
            });

            $table->addColumn('trainer', function ($row) {
                return $row->membership && $row->membership->trainer ? $row->membership->trainer->name : '-';
            });

            $table->addColumn('member_code_number', function ($row) {
                return $row->membership && $row->membership->member ? $row->membership->member->member_code : $row->id;
            });

            $table->addColumn('sales_by_name', function ($row) {
                return $row->sales_by ? $row->sales_by->name : '';
            });

            $table->addColumn('branch_name', function ($row) {
                return $row->membership->member && $row->membership->member->branch ? $row->membership->member->branch->name : '-';
            });

            $table->addColumn('created_by', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });


            $table->rawColumns(['actions', 'placeholder', 'amount', 'membership', 'sales_by', 'invoice_paid', 'status', 'membership_service', 'member', 'created_by', 'trainer', 'review_status', 'discount', 'member_code_number', 'branch_name']);

            return $table->make(true);
        }

        $sales_bies = User::whereHas('roles', function ($q) {
            $q = $q->whereTitle('Sales');
        })->pluck('name', 'id');

        $branches = Branch::pluck('name', 'id');

        if ($employee && $employee->branch_id != NULL) {
            $db_invoices = Invoice::index($data)->whereBranchId($employee->branch_id)->where('status', '!=', 'refund');

            $payments = Payment::whereHas('invoice', function ($q) use ($employee) {
                $q->where('status', '!=', 'refund')->whereBranchId($employee->branch_id);
            })->when(count($data) > 0, function ($q) use ($db_invoices) {
                $q->whereIn('invoice_id', $db_invoices->pluck('id')->toArray());
            })->get();
        } else {
            $db_invoices = Invoice::index($data)->where('status', '!=', 'refund');

            $payments = Payment::whereHas('invoice', function ($q) {
                $q->where('status', '!=', 'refund');
            })->when(count($data) > 0, function ($q) use ($db_invoices) {
                $q->whereIn('invoice_id', $db_invoices->pluck('id')->toArray());
            })->get();
        }



        return view('admin.invoices.settled');
    }
    public function create()
    {
        abort_if(Gate::denies('invoice_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $memberships = Membership::pluck('start_date', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sales_bies = User::whereHas('roles', function ($q) {
            $q = $q->whereTitle('Sales');
        })->pluck('name', 'id');

        return view('admin.invoices.create', compact('memberships', 'sales_bies'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $invoice = Invoice::create($request->all());

        return redirect()->route('admin.invoices.index');
    }

    public function edit(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $memberships = Membership::pluck('start_date', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sales_bies = User::whereHas('roles', function ($q) {
            $q = $q->whereTitle('Sales');
        })->pluck('name', 'id');

        $invoice->load('membership', 'sales_by')->loadSum('payments', 'amount');

        $pricelists = Pricelist::whereStatus('active')->with(['service'])->latest()->get();

        $trainers = User::whereHas('roles', function ($q) {
            $q->where('title', 'Trainer');
        })->whereHas('employee', function ($i) {
            $i->whereStatus('active');
        })->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $setting = Setting::first();

        $accounts = Account::pluck('name', 'id');

        $memberStatuses = MemberStatus::pluck('name', 'id');

        return view('admin.invoices.edit', compact('memberships', 'sales_bies', 'invoice', 'pricelists', 'trainers', 'setting', 'accounts'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        if ($request->edit_membership == 'yes') {
            $membership = $invoice->membership;
            $membership->update([
                'service_pricelist_id'      => $request->service_pricelist_id,
                'start_date'                => $request->start_date,
                'end_date'                  => $request->end_date,
                'trainer_id'                => $request->trainer_id,
                'notes'                     => $request->subscription_notes,
            ]);
        }

        /// Edit Here
        if ($request->input('account_ids')) {
            foreach ($request->account_ids as $key => $account_id) {
                if (isset($invoice->payments[$key])) {
                    if ($request->account_amount[$key] > 0) {
                        $invoice->payments[$key]->update([
                            'account_id'        => $account_id,
                            'amount'            => $request->account_amount[$key],
                            'created_at'        => $request['created_at'] . date('H:i:s'),
                            'created_by_id'     => $invoice->created_by_id == NULL ? Auth()->user()->id : $invoice->created_by_id
                        ]);

                        $invoice->payments[$key]->transaction->update([
                            'transactionable_type'  => 'App\\Models\\Payment',
                            'transactionable_id'    => $invoice->payments[$key]->id,
                            'amount'                => $request->account_amount[$key],
                            'account_id'            => $account_id,
                            'created_by'            => $invoice->created_by_id == NULL ? Auth()->user()->id : $invoice->created_by_id,
                            'created_at'            => $request['created_at'] . date('H:i:s')
                        ]);
                    } else {
                        $invoice->payments[$key]->transaction->delete();
                        $invoice->payments[$key]->delete();
                    }
                } else {
                    if ($request->account_amount[$key] > 0) {
                        $payment = $invoice->payments()->create([
                            'account_id'        => $account_id,
                            'amount'            => $request->account_amount[$key],
                            'sales_by_id'       => $invoice->sales_by_id,
                            'created_at'        => $request['created_at'] . date('H:i:s'),
                            'created_by_id'     => $invoice->created_by_id == NULL ? Auth()->user()->id : $invoice->created_by_id
                        ]);

                        $transaction = Transaction::create([
                            'transactionable_type'  => 'App\\Models\\Payment',
                            'transactionable_id'    => $payment->id,
                            'amount'                => $request->account_amount[$key],
                            'account_id'            => $account_id,
                            'created_at'            => $request['created_at'] . date('H:i:s'),
                            'created_by'            => $invoice->created_by_id == NULL ? Auth()->user()->id : $invoice->created_by_id,
                        ]);
                    }
                }
            }
        } else {
            foreach ($invoice->payments as $key => $payment) {
                $payment->update([
                    'created_at'        => $request['created_at'] . date('H:i:s'),
                    'created_by_id'     => $payment->created_by_id == NULL ? Auth()->user()->id : $payment->created_by_id
                ]);

                $payment->transaction->update([
                    'created_at'            => $request['created_at'] . date('H:i:s'),
                    'created_by'            => $invoice->created_by_id == NULL ? Auth()->user()->id : $invoice->created_by_id,
                ]);
            }
        }

        $invoice->update([
            'created_at'        => $request['created_at'],
            'service_fee'       => $request->input('membership_fee') ?? $invoice->service_fee,
            'discount'          => $request->input('discount_amount') ?? $invoice->discount,
            'discount_notes'    => $request->input('discount_notes') ?? $invoice->discount_notes,
            'net_amount'        => $request->input('net_amount') ?? $invoice->net_amount,
            'status'            => $request->input('amount_pending') && $request->input('amount_pending') > 0 ? 'partial' : 'fullpayment',
            'created_by_id'     => $invoice->created_by_id == NULL ? Auth()->user()->id : $invoice->created_by_id,
            'is_reviewed'       => 0
        ]);

        $this->updated();

        return redirect()->route('admin.invoices.index');
    }

    public function show($id)
    {
        abort_if(Gate::denies('invoice_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $invoice = Invoice::with(['membership' => fn ($q) => $q->with('trainer'), 'sales_by', 'payments', 'created_by'])->withSum('payments', 'amount')->where('id', $id)->first();

        $setting = Setting::first();
        $invoice_template = $setting->invoice;
        $invoice_tmp = [];
        foreach (json_decode($invoice_template, true) as $key => $inv_tmp) {
            $inv_tmp = str_replace('{member_code}', $invoice->membership->member->member_code ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_name}', $invoice->membership->member->name ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_prefix}', $setting->member_prefix  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_email}', $invoice->membership->member->user->email  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_address}', $invoice->membership->member->address->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_phone}', $invoice->membership->member->phone  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{membership_start_date}', $invoice->membership->start_date  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{membership_end_date}', $invoice->membership->end_date  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{membership_sport}', $invoice->membership->sport->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_prefix}', $setting->invoice_prefix  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_number}', $invoice->id  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_date}', $invoice->created_at->toFormattedDateString()  ?? '-', $inv_tmp);
            $inv_tmp = str_replace('{invoice_status}', Invoice::STATUS_SELECT[$invoice->status]  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{payment_method}', $invoice->account  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{next_due_date}', '01-01-2022'  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{sales_by}', $invoice->membership->sales_by->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{created_by}', $invoice->created_by->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{trainer}', $invoice->membership->trainer->name  ?? 'No trainer', $inv_tmp);
            $inv_tmp = str_replace('{gym_phone_numbers}', $setting->phone_numbers  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_landline}', $setting->landline  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_address}', $setting->address  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_email}', $setting->email  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_name}', $setting->name  ?? '', $inv_tmp);
            $invoice_tmp[$key] = $inv_tmp;
        }

        return view('admin.invoices.show', compact('invoice', 'setting', 'invoice_tmp'));
    }



    public function destroy(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoice->load('payments');

        foreach ($invoice->payments as $key => $payment) {
            $payment->account->balance -= $payment->amount;
            $payment->account->save();
        }

        $invoice->delete();

        return back();
    }

    public function massDestroy(MassDestroyInvoiceRequest $request)
    {
        Invoice::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function printInvoice(Request $request, $id)
    {
        $invoice = $this->invoice($id);

        $text = view('vendor.invoices.templates.default', [
            'invoice'       => $invoice,
        ]);

        PDF::SetTitle('Invoice #' . $id);
        PDF::AddPage();
        PDF::SetFont('Dejavu Sans', '', 9);
        PDF::writeHTML($text);
        PDF::Output('invoice.pdf');
    }

    public function sendInvoice($id)
    {
        $invoice = $this->invoice($id);
        $setting = Setting::first();
        $invoice->filename($setting->invoice_prefix . $id)->save('public');
        $file_name = $invoice->url();
        $db_invoice = Invoice::findOrFail($id);
        try {
            $response = Http::withToken(config('marketing.w_a_token'))->post('https://api.wassenger.com/v1/files?reference=' . Str::random(10), [
                'url' => $file_name
            ]);
            if ($response->json()['status'] == 409) {
                $imageID = $response->json()['meta']['file'];
            } else {
                $imageID = $response->json()[0]['id'];
            }

            Http::withToken(config('marketing.w_a_token'))->post('https://api.wassenger.com/v1/messages', [
                'phone' => '+2' . $db_invoice->membership->member->phone,
                'message' => 'Invoice : #' . $setting->invoice_prefix . $id,
                'media' => ['file' => $imageID]
            ]);
        } catch (\Exception $ex) {
            dd($ex->getMessage());
        }

        $this->sent_successfully();
        return back();
    }


    public function downloadInvoice(Request $request, $id)
    {
        $invoice = $this->invoice($id);
        return $invoice->download();
    }


    public function invoice($id)
    {
        $db_invoice = Invoice::with(['membership' => fn ($q) => $q->with('trainer'), 'sales_by', 'payments', 'created_by'])->withSum('payments', 'amount')->findOrFail($id);
        $setting = Setting::first();
        $invoice_template = $setting->invoice;
        $invoice_tmp = [];
        foreach (json_decode($invoice_template, true) as $key => $inv_tmp) {
            $inv_tmp = str_replace('{member_code}', $db_invoice->membership->member->member_code ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_name}', $db_invoice->membership->member->name ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_prefix}', $setting->member_prefix  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_email}', $db_invoice->membership->member->user->email  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_address}', $db_invoice->membership->member->address->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_phone}', $db_invoice->membership->member->phone  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_prefix}', $setting->invoice_prefix  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_number}', $db_invoice->id  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{membership_start_date}', $db_invoice->membership->start_date  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{membership_end_date}', $db_invoice->membership->end_date  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{membership_sport}', $db_invoice->membership->sport->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_date}', $db_invoice->created_at->toFormattedDateString()  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_status}', 'Done'  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{payment_method}', $db_invoice->payment_method  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{next_due_date}', '01-01-2022'  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{sales_by}', $db_invoice->membership->sales_by->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{created_by}', $db_invoice->created_by->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{trainer}', $db_invoice->membership->trainer->name  ?? 'no Trainer', $inv_tmp);
            $inv_tmp = str_replace('{gym_phone_numbers}', $setting->phone_numbers  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_landline}', $setting->landline  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_address}', $setting->address  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_email}', $setting->email  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_name}', $setting->name  ?? '', $inv_tmp);
            $invoice_tmp[$key] = $inv_tmp;
        }
        $customer = new Buyer([
            'name'              => $db_invoice->membership->member->name,
            'custom_fields'     => $invoice_tmp['right_section']
        ]);

        $client = new Party([
            'name'          => $setting->name,
            'custom_fields' => $invoice_tmp['left_section']
        ]);

        $invoice_id = $db_invoice->id;

        $item = [
            'item'      => [
                'title'         => $db_invoice->membership->service_pricelist->name,
                'pricePerUnit'  => $db_invoice->service_fee,
                'discount'      => $db_invoice->discount
            ]
        ];

        $invoice = [
            'seller'                => $client,
            'buyer'                 => $customer,
            'serial_number'         => $setting->invoice_prefix . $db_invoice->id,
            'date'                  => $db_invoice->created_at->toFormattedDateString(),
            'logo'                  => public_path('images/' . Setting::first()->menu_logo),
            'notes'                 => $invoice_tmp['footer'],
            'items'                 => $item
        ];
        // $invoice = LaravelInvoices::make()
        //     ->buyer($customer)
        //     ->seller($client)
        //     ->serialNumberFormat($setting->invoice_prefix. $db_invoice->id)
        //     ->date($db_invoice->created_at)
        //     ->logo(public_path('images/'. Setting::first()->menu_logo))
        //     ->dateFormat('Y/m/d')
        //     ->currencySymbol('L.E')
        //     ->currencyCode('EGP')
        //     ->notes($invoice_tmp['footer'])
        //     ->addItem($item);
        // $invoice = $this->invoice($id);
        $invoice['invoice'] = $db_invoice;
        return $invoice;
    }

    public function refund($id)
    {
        abort_if(Gate::denies('refund_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoice = Invoice::withSum('payments', 'amount')->findOrFail($id);

        $refund_reasons = RefundReason::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.invoices.refund', compact('invoice', 'refund_reasons'));
    }

    public function storeRefund(Request $request, $id)
    {
        abort_if(Gate::denies('refund_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            DB::beginTransaction();

            $invoice = Invoice::withSum('payments', 'amount')->findOrFail($id);

            $refund = Refund::create([
                'invoice_id' => $invoice->id,
                'refund_reason_id' => $request->refund_reason_id,
                'amount' => $request->amount,
                'account_id' => $request->account_id,
                'created_by_id' => Auth()->user()->id,
                'status' => 'pending'
            ]);

            // $invoice->membership->status = 'refunded';
            // $invoice->membership->save();

            // $refund->account->balance = $refund->account->balance - $request->amount;
            // $refund->account->save();


            // $transaction = Transaction::create([
            //     'transactionable_type' => 'App\\Models\\Refund',
            //     'transactionable_id' => $refund->id,
            //     'amount' => $refund->amount,
            //     'account_id' => $request->account_id,
            //     'created_by' => auth()->user()->id,
            // ]);

            // $invoice->status = 'refund';
            // $invoice->save();

            $this->created();
            DB::commit();
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
        }
        return redirect()->route('admin.refunds.index');
    }

    public function payment($id)
    {
        abort_if(Gate::denies('payment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $selected_branch = isset(Auth()->user()->employee) ? Auth()->user()->employee->branch : Branch::first();


        $invoice = Invoice::withSum('payments', 'amount')->findOrFail($id);

        // $accounts = Account::pluck('name','id');
        $accounts = Account::where('branch_id', $selected_branch->id)->pluck('name', 'id');

        $sales_bies = User::whereHas('roles', function ($q) {
            $q = $q->whereTitle('Sales');
        })->pluck('name', 'id');

        $memberStatuses = Status::pluck('name', 'id');

        return view('admin.payments.create', compact('invoice', 'accounts', 'sales_bies', 'memberStatuses'));
    }
    public function paymentDuePayments($id)
    {
        abort_if(Gate::denies('payment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $selected_branch = isset(Auth()->user()->employee) ? Auth()->user()->employee->branch : Branch::first();


        $invoice = Invoice::withSum('payments', 'amount')->findOrFail($id);

         $accounts = Account::pluck('name','id');

        $sales_bies = User::whereHas('roles', function ($q) {
            $q = $q->whereTitle('Sales');
        })->pluck('name', 'id');

        $memberStatuses = Status::pluck('name', 'id');

        return view('admin.payments.create', compact('invoice', 'accounts', 'sales_bies', 'memberStatuses'));
    }

    public function storePayment(Request $request, $id)
    {
        $invoice = Invoice::with(['membership' => fn ($q) => $q->with('member')])
            ->withSum('payments', 'amount')
            ->findOrFail($id);

        $member = $invoice->membership->member;
        $sales_by_id = $member->sales_by_id;

        if ($request['amount_pending'] == 0) {
            $invoice->membership->member->update(['status_id' => NULL]);

            $invoice->update([
                'status' => 'fullpayment'
            ]);
        }

        foreach ($request['account_amount'] as $key => $account_amount) {
            if ($account_amount > 0) {

                $payment = Payment::create([
                    'account_id'    => $request->account_ids[$key],
                    'amount'        => $account_amount,
                    'invoice_id'    => $invoice->id,
                    'sales_by_id'   => $request['sales_by_id'] ?? $sales_by_id,
                    'notes'         => $request['notes'],
                    'created_at'    => $request->payment_date
                ]);

                $transaction = Transaction::create([
                    'transactionable_type'  => 'App\\Models\\Payment',
                    'transactionable_id'    => $payment->id,
                    'amount'                => $account_amount,
                    'account_id'            => $request->account_ids[$key],
                    'created_by'            => $request['sales_by_id'] ?? $sales_by_id,
                    'created_at'            => $request->payment_date . date('H:i:s')
                ]);

                $payment->account->balance = $payment->account->balance + $payment->amount;
                $payment->account->save();
            }
        }

        if ($invoice->membership->reminders()->whereType('due_payment')->count() >  0) {
            $invoice->membership->reminders()->whereType('due_payment')->first()->delete();
        }

        // Due payment Reminder
        if ($request->received_amount < $invoice->rest) {
            if (!is_null($request->due_date)) {
                Reminder::create([
                    'type'              => 'due_payment',
                    'membership_id'     => $invoice->membership_id,
                    'lead_id'           => $invoice->membership->member_id,
                    'due_date'          => $request->due_date,
                    'user_id'           => $sales_by_id,
                ]);
            }
        }


        if ($invoice->payments_sum_amount == $invoice->net_amount) {
            $invoice->update([
                'status' => 'fullpayment'
            ]);

            // Upgrade Reminder
            // Reminder::create([
            //     'type'              => 'upgrade',
            //     'membership_id'     => $invoice->membership_id,
            //     'lead_id'           => $invoice->membership->member_id,
            //     'due_date'          => $request->due_date,
            //     'user_id'           => $sales_by_id,
            // ]);
        }

        $invoice->update([
            'is_reviewed'   => 0
        ]);

        $this->created();

        return redirect()->route('admin.invoices.index');
    }

    public function changeInvoice()
    {
        return view('admin.invoices.change');
    }

    public function storeChangeInvoice(Request $request)
    {
        $invoices = explode(",", $request->invoice_ids);
        // $invoices = range(1, 958);
        foreach ($invoices as $key => $invoice) {
            $inv = Invoice::find($invoice);
            if ($inv) {
                $inv->created_at = $request->date;
                $inv->updated_at = $request->date;
                $inv->save();

                foreach ($inv->payments as $key => $payment) {
                    $payment->created_at = $request->date;
                    $payment->updated_at = $request->date;
                    $payment->save();
                    if ($payment->transaction) {
                        $payment->transaction->created_at = $request->date;
                        $payment->transaction->updated_at = $request->date;
                        $payment->transaction->save();
                    }
                }
            }
        }

        $this->created();
        return back();
    }

    public function showSupervisor($id, $alt_id)
    {
        abort_if(Gate::denies('invoice_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $invoice = Invoice::with(['membership' => fn ($q) => $q->with('trainer'), 'sales_by', 'payments', 'created_by'])->withSum('payments', 'amount')->where('id', $id)->first();

        $setting = Setting::first();
        $invoice_template = $setting->invoice;
        $invoice_tmp = [];
        foreach (json_decode($invoice_template, true) as $key => $inv_tmp) {
            $inv_tmp = str_replace('{member_code}', $invoice->membership->member->member_code ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_name}', $invoice->membership->member->name ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_prefix}', $setting->member_prefix  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_email}', $invoice->membership->member->user->email  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_address}', $invoice->membership->member->address->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_phone}', $invoice->membership->member->phone  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_prefix}', $setting->invoice_prefix  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_number}', $alt_id ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_date}', $invoice->created_at->toFormattedDateString()  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_status}', Invoice::STATUS_SELECT[$invoice->status]  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{payment_method}', $invoice->account  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{next_due_date}', '01-01-2022'  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{sales_by}', $invoice->membership->sales_by->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{created_by}', $invoice->created_by->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{trainer}', $invoice->membership->trainer->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_phone_numbers}', $setting->phone_numbers  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_landline}', $setting->landline  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_address}', $setting->address  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_email}', $setting->email  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_name}', $setting->name  ?? '', $inv_tmp);
            $invoice_tmp[$key] = $inv_tmp;
        }

        return view('admin.invoices.show', compact('invoice', 'setting', 'invoice_tmp', 'alt_id'));
    }

    public function invoiceSupervisor($id, $alt_id)
    {
        $db_invoice = Invoice::findOrFail($id);
        $setting = Setting::first();
        $invoice_template = $setting->invoice;
        $invoice_tmp = [];
        foreach (json_decode($invoice_template, true) as $key => $inv_tmp) {
            $inv_tmp = str_replace('{member_code}', $db_invoice->membership->member->member_code ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_name}', $db_invoice->membership->member->name ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_prefix}', $setting->member_prefix  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_email}', $db_invoice->membership->member->user->email  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_address}', $db_invoice->membership->member->address->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{member_phone}', $db_invoice->membership->member->phone  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_prefix}', $setting->invoice_prefix  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_number}', $alt_id  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_date}', $db_invoice->created_at->toFormattedDateString()  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{invoice_status}', 'Done'  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{payment_method}', $db_invoice->payment_method  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{next_due_date}', '01-01-2022'  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{sales_by}', $db_invoice->membership->sales_by->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{created_by}', $db_invoice->created_by->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{trainer}', $db_invoice->membership->trainer->name  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_phone_numbers}', $setting->phone_numbers  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_landline}', $setting->landline  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_address}', $setting->address  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_email}', $setting->email  ?? '', $inv_tmp);
            $inv_tmp = str_replace('{gym_name}', $setting->name  ?? '', $inv_tmp);
            $invoice_tmp[$key] = $inv_tmp;
        }

        $customer = new Buyer([
            'name'              => $db_invoice->membership->member->name,
            'custom_fields'     => $invoice_tmp['right_section']
        ]);

        $client = new Party([
            'name'          => $setting->name,
            'custom_fields' => $invoice_tmp['left_section']
        ]);

        $invoice_id = $db_invoice->id;

        $item = (new InvoiceItem())
            ->title($db_invoice->membership->service_pricelist->service->name)
            ->pricePerUnit($db_invoice->service_fee)
            ->discount($db_invoice->discount);

        $invoice = LaravelInvoices::make()
            ->buyer($customer)
            ->seller($client)
            ->serialNumberFormat($setting->invoice_prefix . $db_invoice->id)
            ->date($db_invoice->created_at)
            ->logo(public_path('images/' . Setting::first()->menu_logo))
            ->dateFormat('Y/m/d')
            ->currencySymbol('L.E')
            ->currencyCode('EGP')
            ->notes($invoice_tmp['footer'])
            ->addItem($item);
        return $invoice;
    }

    public function printInvoiceSupervisor(Request $request, $id, $alt_id)
    {
        $invoice = $this->invoiceSupervisor($id, $alt_id);
        // $invoice->save('public')->filename('#'. $setting->invoice_prefix . $invoice_id);
        return $invoice->stream();
    }

    public function duePaymentsInvoices(Request $request , $id)
    {
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

        $sale = User::with([
            'invoices' => fn ($q) => $q
                ->where('created_at', '>=', $from)
                ->where('created_at', '<=', $to)
                ->withSum('payments', 'amount')
                ->whereStatus('partial')
                ->whereHas('membership')
                ->latest()
        ])
            ->whereHas('invoices', function ($x) use ($from, $to) {
                $x->whereStatus('partial')
                    ->where('created_at', '>=', $from)
                    ->where('created_at', '<=', $to)
                    ->whereHas('membership');
            })
            ->whereHas('roles', function ($i) {
                $i->where('title', 'sales');
            })
            ->find($id);
        return view('admin.reports.due_payments_invoice', compact('sale'));
    }

    public function payments($id)
    {
        $invoice = Invoice::with('payments')->withCount('payments')->withSum('payments', 'amount')->findOrFail($id);

        return view('admin.invoices.payments', compact('invoice'));
    }

    public function partial(Request $request)
    {
        abort_if(Gate::denies('invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);
        $setting = Setting::first();
        if ($request->ajax()) {
            $query = Invoice::index($data)
                ->whereStatus('partial')
                ->whereHas('membership')
                ->with(['membership', 'membership.service_pricelist', 'membership.member', 'sales_by', 'payments', 'created_by'])
                ->withSum('payments', 'amount')
                ->select(sprintf('%s.*', (new Invoice())->table));

            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'invoice_show';
                $editGate = 'invoice_edit';
                $crudRoutePart = 'invoices';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? '#' . $row->id : '';
            });

            $table->editColumn('discount', function ($row) {
                return $row->discount ? $row->discount . ' EGP' . '<br />' . ($row->discount_notes !== NULL ? $row->discount_notes : '<span class="badge badge-danger">No Notes</span>') : 0;
            });

            $table->addColumn('member', function ($row) use ($setting) {
                return $row->membership && $row->membership->member ? '<a href="' . route('admin.members.show', $row->membership->member_id) . '" target="_blank">' . $setting->member_prefix . $row->membership->member->member_code . ' <br> ' . $row->membership->member->name . '<br>' . $row->membership->member->phone . '</a>' : '';
            });

            $table->editColumn('amount', function ($row) {
                return $row->net_amount ? "<span class='text-success font-weight-bold'>" . trans('global.net') . "</span>" . ' : ' . $row->net_amount . ' EGP' . '<br/>' .  "<span class='text-primary font-weight-bold'>" . trans('invoices::invoice.paid') . "</span>" . ' : ' . $row->payments->sum('amount') . ' EGP <br />' . "<span class='text-danger font-weight-bold'>" . trans('global.rest') . "</span>" . ' : ' . $row->rest . ' EGP' : '';
            });

            $table->editColumn('status', function ($row) {
                return $row->status == 'fullpayment' ? '<span class="badge badge-success p-2">' . Invoice::STATUS_SELECT[$row->status] . '</span>' : '<span class="badge badge-danger p-2">' . Invoice::STATUS_SELECT[$row->status] . '</span>';
            });

            if (config('domains')[config('app.url')]['is_reviewed_invoices'] == true) {
                $table->editColumn('review_status', function ($row) {
                    if ($row->is_reviewed == 0) {
                        return "<span class='badge badge-warning px-2 py-2'>" . trans('global.not_reviewed') . "</span>";
                    } else {
                        return "<span class='badge badge-success px-2 py-2'>" . trans('global.is_reviewed') . "</span>";
                    }
                });
            }

            $table->addColumn('membership_service', function ($row) {
                return $row->membership && $row->membership->service_pricelist && $row->membership->service_pricelist->service && $row->membership->service_pricelist->service->service_type ? $row->membership->service_pricelist->name . '<br />' . trans('cruds.invoice.fields.service_fee') . ' : ' . $row->service_fee . ' EGP' . '<br>' . '<span class="badge p-2 badge-' . Membership::MEMBERSHIP_STATUS_COLOR[$row->membership->membership_status] . '"">' . Membership::MEMBERSHIP_STATUS[$row->membership->membership_status] . '</span>' : '-';
            });

            $table->addColumn('trainer', function ($row) {
                return $row->membership && $row->membership->trainer ? $row->membership->trainer->name : '-';
            });

            $table->addColumn('sales_by_name', function ($row) {
                return $row->sales_by ? $row->sales_by->name : '';
            });

            $table->addColumn('created_by', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'amount', 'membership', 'sales_by', 'invoice_paid', 'status', 'membership_service', 'member', 'created_by', 'trainer_by', 'review_status', 'discount']);

            return $table->make(true);
        }

        $sales_bies = User::whereHas('roles', function ($q) {
            $q = $q->whereTitle('Sales');
        })->pluck('name', 'id');

        $invoices = Invoice::index($data)->whereStatus('partial')->with('payments', function ($i) {
            $i->whereIn('invoice_id', $this->pluck('id'));
        });

        $payments = Payment::whereIn('invoice_id', $invoices->pluck('id'))->sum('amount');

        return view('admin.invoices.partial', compact('sales_bies', 'invoices', 'payments'));
    }

    public function export(Request $request)
    {
        return Excel::download(new InvoicesExport($request), 'Invoices.xlsx');
    }

    public function exportPartial(Request $request)
    {
        return Excel::download(new PartialInvoicesExport($request), 'Partial-invoice.xlsx');
    }

    public function updateReviewedStatus(Request $request, Invoice $invoice)
    {
        $current_invoice = $invoice;
        $invoice->update(['is_reviewed' => $current_invoice->is_reviewed == 0 ? 1 : 0]);
        $this->updated();
        return back();
    }

    public function settlementInvoice(Request $request, $id)
    {
        $invoice = Invoice::with(['payments'])->withSum('payments', 'amount')->findOrFail($id);
        $new_net = $invoice->payments_sum_amount ?? 0;
       
        $invoice->update([
            'status'        => 'settlement',
            'net_amount'    => $new_net
        ]);
        // dd($invoice);
        $this->updated();

        return back();
    }

    public function settlement(Request $request)
    {
        abort_if(Gate::denies('settlement_invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);
        $setting = Setting::first();
        if ($request->ajax()) {
            $query = Invoice::index($data)
                ->whereStatus('settlement')
                ->whereHas('membership')
                ->with(['membership', 'membership.service_pricelist', 'membership.member', 'sales_by', 'payments', 'created_by'])
                ->withSum('payments', 'amount')
                ->select(sprintf('%s.*', (new Invoice())->table));

            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'invoice_show';
                $editGate = 'invoice_edit';
                $crudRoutePart = 'invoices';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? '#' . $row->id : '';
            });

            $table->editColumn('discount', function ($row) {
                return $row->discount ? $row->discount . ' EGP' . '<br />' . ($row->discount_notes !== NULL ? $row->discount_notes : '<span class="badge badge-danger">No Notes</span>') : 0;
            });

            $table->addColumn('member', function ($row) use ($setting) {
                return $row->membership && $row->membership->member ? '<a href="' . route('admin.members.show', $row->membership->member_id) . '" target="_blank">' . $setting->member_prefix . $row->membership->member->member_code . ' <br> ' . $row->membership->member->name . '<br>' . $row->membership->member->phone . '</a>' : '';
            });

            $table->editColumn('amount', function ($row) {
                return $row->net_amount ? "<span class='text-success font-weight-bold'>" . trans('global.net') . "</span>" . ' : ' . $row->net_amount . ' EGP' . '<br/>' .  "<span class='text-primary font-weight-bold'>" . trans('invoices::invoice.paid') . "</span>" . ' : ' . $row->payments->sum('amount') . ' EGP <br />' . "<span class='text-danger font-weight-bold'>" . trans('global.rest') . "</span>" . ' : ' . $row->rest . ' EGP' : '';
            });

            $table->editColumn('status', function ($row) {
                return $row->status == 'fullpayment' ? '<span class="badge badge-success p-2">' . Invoice::STATUS_SELECT[$row->status] . '</span>' : '<span class="badge badge-danger p-2">' . Invoice::STATUS_SELECT[$row->status] . '</span>';
            });

            if (config('domains')[config('app.url')]['is_reviewed_invoices'] == true) {
                $table->editColumn('review_status', function ($row) {
                    if ($row->is_reviewed == 0) {
                        return "<span class='badge badge-warning px-2 py-2'>" . trans('global.not_reviewed') . "</span>";
                    } else {
                        return "<span class='badge badge-success px-2 py-2'>" . trans('global.is_reviewed') . "</span>";
                    }
                });
            }

            $table->addColumn('membership_service', function ($row) {
                return $row->membership && $row->membership->service_pricelist && $row->membership->service_pricelist->service && $row->membership->service_pricelist->service->service_type ? $row->membership->service_pricelist->name . '<br />' . trans('cruds.invoice.fields.service_fee') . ' : ' . $row->service_fee . ' EGP' . '<br>' . '<span class="badge p-2 badge-' . Membership::MEMBERSHIP_STATUS_COLOR[$row->membership->membership_status] . '"">' . Membership::MEMBERSHIP_STATUS[$row->membership->membership_status] . '</span>' : '-';
            });

            $table->addColumn('trainer', function ($row) {
                return $row->membership && $row->membership->trainer ? $row->membership->trainer->name : '-';
            });

            $table->addColumn('sales_by_name', function ($row) {
                return $row->sales_by ? $row->sales_by->name : '';
            });

            $table->addColumn('branch_name', function ($row) {
                return $row->membership->member && $row->membership->member->branch ? $row->membership->member->branch->name : '-';
            });

            $table->addColumn('created_by', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'amount', 'membership', 'sales_by', 'invoice_paid', 'status', 'membership_service', 'member', 'created_by', 'trainer_by', 'review_status', 'discount', 'branch_name']);

            return $table->make(true);
        }

        $sales_bies = User::whereHas('roles', function ($q) {
            $q = $q->whereTitle('Sales');
        })->pluck('name', 'id');

        $invoices = Invoice::index($data)->whereStatus('settlement')->with('payments', function ($i) {
            $i->whereIn('invoice_id', $this->pluck('id'));
        });

        $payments = Payment::whereIn('invoice_id', $invoices->pluck('id'))->sum('amount');

        $branches = Branch::pluck('name', 'id');

        return view('admin.invoices.settlement', compact('sales_bies', 'invoices', 'payments', 'branches'));
    }
}
