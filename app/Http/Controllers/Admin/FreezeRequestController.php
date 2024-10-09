<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting;
use App\Models\UserAlert;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Models\FreezeRequest;
use App\Exports\FreezesExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\StoreFreezeRequestRequest;
use App\Http\Requests\UpdateFreezeRequestRequest;
use App\Http\Requests\MassDestroyFreezeRequestRequest;
use App\Models\Branch;

class FreezeRequestController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('freeze_request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','branch_id','_']);
        $settings = Setting::first();
        if ($request->ajax()) {
            $query = FreezeRequest::index($data)
                                    ->whereIn('status',['pending','confirmed','rejected'])
                                    ->with(['membership', 'created_by','membership.service_pricelist','membership.member','membership.member.branch'])
                                    ->latest()
                                    ->select(sprintf('%s.*', (new FreezeRequest())->table));
            if(isset($request->branch_id)){
                $query=$query->whereHas('membership.member.branch', function ($q) use ($request) {
                    $q->whereIn('id', $request->branch_id);});
            }
            //            dd($query->limit(1)->get());
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'freeze_request_show';
                $editGate = 'freeze_request_edit';
                $deleteGate = 'freeze_request_delete';
                $crudRoutePart = 'freeze-requests';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->addColumn('member', function ($row) use($settings) {
                return $row->membership && $row->membership->member ?  '<a href="'.route('admin.members.show',$row->membership->member_id).'" target="_blank">'.$settings->member_prefix . $row->membership->member->member_code . "<br>" .$row->membership->member->name.'</a>' : '-';
            });

            $table->addColumn('membership_service', function ($row) {
                return $row->membership && $row->membership->service_pricelist ? $row->membership->service_pricelist->name : '-';
            });

            $table->addColumn('consumed', function ($row) {
                $now = Carbon::now();
                $start = Carbon::parse($row->start_date);
                $diff = $now->diffInDays($start);
                if($diff > $row->freeze){
                    return $row->status == 'confirmed' ? $row->freeze . ' ' . trans('global.day_or_days') : '-';
                }else{
                    return $row->status == 'confirmed' ? $diff . ' ' . trans('global.day_or_days') : '-';
                }
            });

            $table->editColumn('freeze', function ($row) {
                return $row->freeze ? $row->freeze : '-';
            });

            $table->editColumn('status', function ($row) {
                return $row->status ? "<span class='badge px-3 py-2 badge-".FreezeRequest::STATUS_COLOR[$row->status]."'>".ucfirst($row->status)."</span>" : '';
            });

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '-';
            });

            $table->addColumn('branch_name', function ($row) {
                return $row->membership->member && $row->membership->member->branch ? $row->membership->member->branch->name : '-';
            });

            $table->addColumn('is_retroactive', function ($row) {
                return "<span class='badge px-3 py-2 badge-".FreezeRequest::IS_RETROACTIVE_COLOR[$row->is_retroactive]."'>".FreezeRequest::IS_RETROACTIVE[$row->is_retroactive]."</span>" ?? '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'membership_service', 'created_by','member','member_code','status','is_retroactive','branch_name']);

            return $table->make(true);
        }

        $branches = Branch::pluck('name','id');

        return view('admin.freezeRequests.index',compact('branches'));
    }

    public function create()
    {
        abort_if(Gate::denies('freeze_request_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $memberships = Membership::with(['member','service_pricelist','member.branch'])->whereNotIn('status',['expired','refunded'])->get();

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');


        return view('admin.freezeRequests.create', compact('memberships', 'created_bies'));
    }

    public function store(StoreFreezeRequestRequest $request)
    {
        $freezeRequest = FreezeRequest::create([
            'membership_id'     => $request->membership_id,
            'freeze'            => $request->freeze,
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date,
            'status'            => 'pending',
            'created_by_id'     => auth()->user()->id,
            'is_retroactive'    => date('Y-m-d') == date('Y-m-d',strtotime($request['start_date'])) ? false : true
        ]);

        // Freeze Request Automatically
        $setting = Setting::first();
        if ( $setting->freeze_request == 1) {
            $freezeRequest->membership->update([
                'end_date' => $setting->freeze_duration == 'days' ? date('Y-m-d',strtotime($freezeRequest->membership->end_date . ' + ' . $freezeRequest->freeze . ' Days')) : date('Y-m-d',strtotime($freezeRequest->membership->end_date . ' + ' . $freezeRequest->freeze . ' Week')),
            ]);
            $freezeRequest->status = 'confirmed';
            $freezeRequest->save();

            ////////////// Alerts
            $user_alert = UserAlert::create([
                'alert_text'        => 'Freeze request #'.$freezeRequest->id.' Has Been '.$freezeRequest->status,
                'alert_link'        => route('admin.freeze-requests.show',$freezeRequest->id),
            ]);
            
            $admins = User::whereHas('roles', function($q) {
                $q = $q->whereIn('title', ['Developer','Sales','Receptionist']);
            })->pluck('name', 'id');

            foreach($admins as $id => $admin) 
            {
                DB::table('user_user_alert')->insert(['user_alert_id' => $user_alert->id, 'user_id' => $id, 'read' => 0]);
            }
            //////////////
        }

        // From controller.php
        $this->adjustMembership($freezeRequest->membership);


        return redirect()->route('admin.memberships.index');
    }

    public function edit(FreezeRequest $freezeRequest)
    {
        abort_if(Gate::denies('freeze_request_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $memberships = Membership::pluck('start_date', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $freezeRequest->load('membership', 'created_by');
        $now = Carbon::now();
        $start = Carbon::parse($freezeRequest->start_date);
        $end = Carbon::parse($freezeRequest->end_date);
        $diff = $now->diffInDays($start);

        if($diff > $freezeRequest->freeze){
            $diff = $end->diffInDays($start);
        }

        return view('admin.freezeRequests.edit', compact('memberships', 'created_bies', 'freezeRequest', 'diff'));
    }

    public function update(UpdateFreezeRequestRequest $request, FreezeRequest $freezeRequest)
    {   
        $freezeRequest->update([        
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date,
            'freeze'            => $request->freeze,
            'is_retroactive'    => date('Y-m-d') <= date('Y-m-d',strtotime($request['start_date'])) ? false : true
        ]);

        return redirect()->route('admin.freeze-requests.index');
    }

    public function show(FreezeRequest $freezeRequest)
    {
        abort_if(Gate::denies('freeze_request_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $freezeRequest->load('membership', 'created_by');

        return view('admin.freezeRequests.show', compact('freezeRequest'));
    }

    public function destroy(FreezeRequest $freezeRequest)
    {
        abort_if(Gate::denies('freeze_request_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($freezeRequest->status == 'confirmed'){
            $freezeRequest->membership->end_date = date('Y-m-d', strtotime('-'.$freezeRequest->freeze.' days', strtotime($freezeRequest->membership->end_date)));
            $freezeRequest->membership->save();
        }
        $freezeRequest->delete();
        return back();
    }

    public function massDestroy(MassDestroyFreezeRequestRequest $request)
    {
        FreezeRequest::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function confirm($id)
    {
        DB::transaction(function () use ($id) {
            $setting = Setting::first();
            $freezeRequest = FreezeRequest::with('membership')->findOrFail($id);

            $freezeRequest->membership->update([
                'end_date' =>$setting->freeze_duration == 'days' ? date('Y-m-d',strtotime($freezeRequest->membership->end_date . ' + ' . $freezeRequest->freeze . ' Days')) : date('Y-m-d',strtotime($freezeRequest->membership->end_date . ' + ' . $freezeRequest->freeze . ' Week')),
            ]);

            $freezeRequest->status = 'confirmed';
            $freezeRequest->save();

            $this->adjustMembership($freezeRequest->membership);

            ////////////// Alerts
            $user_alert = UserAlert::create([
                'alert_text'        => 'Freeze request #'.$freezeRequest->id.' Has Been '.$freezeRequest->status,
                'alert_link'        => route('admin.freeze-requests.show',$freezeRequest->id),
            ]);
            
            $admins = User::whereHas('roles', function($q) {
                $q = $q->whereIn('title', ['Developer','Sales','Receptionist']);
            })->pluck('name', 'id');

            foreach($admins as $id => $admin) 
            {
                DB::table('user_user_alert')->insert(['user_alert_id' => $user_alert->id, 'user_id' => $id, 'read' => 0]);
            }
            //////////////
            
            $this->sent_successfully();
        });
        
        return back();
        
    }

    public function reject($id)
    {
        $freezeRequest = FreezeRequest::with('membership')->findOrFail($id);
        $freezeRequest->status = 'rejected';
        $freezeRequest->save();

        ////////////// Alerts
        $user_alert = UserAlert::create([
            'alert_text'        => 'Member request #'.$freezeRequest->id.' Has Been '.$freezeRequest->status,
            'alert_link'        => route('admin.freeze-requests.show',$freezeRequest->id),
        ]);
        
        $admins = User::whereHas('roles', function($q) {
            $q = $q->whereIn('title', ['Developer','Sales','Receptionist']);
        })->pluck('name', 'id');

        foreach($admins as $id => $admin) 
        {
            DB::table('user_user_alert')->insert(['user_alert_id' => $user_alert->id, 'user_id' => $id, 'read' => 0]);
        }
        //////////////

        $this->sent_successfully();
        return back();
    }

    public function freeze(Request $request)
    {
        abort_if(Gate::denies('freeze_request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','_']);
        if ($request->ajax()) {
            $query = FreezeRequest::index($data)->with([
                                        'membership',
                                        'created_by',
                                        'membership.service_pricelist',
                                        'membership.member'
                                    ])->whereStatus('confirmed')->select(sprintf('%s.*', (new FreezeRequest())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'freeze_request_show';
                $editGate = 'freeze_request_edit';
                $deleteGate = 'freeze_request_delete';
                $crudRoutePart = 'freeze-requests';

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

            $table->addColumn('member_code', function ($row) {
                return $row->membership && $row->membership->member ?  '<a href="'.route('admin.members.show',$row->membership->member_id).'" target="_blank">'.\App\Models\Setting::first()->member_prefix.$row->membership->member->member_code.'</a>' : '-';
            });

            $table->addColumn('member', function ($row) {
                return $row->membership && $row->membership->member ?  '<a href="'.route('admin.members.show',$row->membership->member_id).'" target="_blank">'.$row->membership->member->name.'</a>' : '-';
            });

            $table->addColumn('membership_service', function ($row) {
                return $row->membership && $row->membership->service_pricelist ? $row->membership->service_pricelist->name : '-';
            });

            $table->editColumn('freeze', function ($row) {
                return $row->freeze ? $row->freeze : '-';
            });

            $table->editColumn('status', function ($row) {
                return $row->status ? FreezeRequest::STATUS_SELECT[$row->status] : '';
            });

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '-';
            });

            $table->addColumn('is_retroactive', function ($row) {
                return "<span class='badge px-3 py-2 badge-".FreezeRequest::IS_RETROACTIVE_COLOR[$row->is_retroactive]."'>".FreezeRequest::IS_RETROACTIVE[$row->is_retroactive]."</span>" ?? '';
            });

            $table->rawColumns(['actions', 'placeholder', 'membership_service', 'created_by','member','member_code','status','is_retroactive']);

            return $table->make(true);
        }

        return view('admin.freezeRequests.freeze');
    }

    public function export(Request $request)
    {
        return Excel::download(new FreezesExport($request), 'Freezes.xlsx');
    }

    public function getMembershipDetails(Request $request,$id,$date,$freeze)
    {
        $setting = Setting::first();
        $membership = Membership::with(['member','service_pricelist','freezeRequests'])->find($id);
        if ($setting->freeze_duration == 'days') {
            $end_date = date('Y-m-d', strtotime($date . ' + ' . $freeze .  ' Days'));
        }else{
            $end_date = date('Y-m-d', strtotime($date . ' + ' . $freeze .  ' Week'));
        }
        
        return response()->json([
            'membership'        => $membership,
            'member'            => $membership->member,
            'freeze_remained'   => $membership->service_pricelist->freeze_count - $membership->freezeRequests()->whereStatus('confirmed')->sum('freeze'),
            'consumed_freeze'   => $membership->freezeRequests()->whereStatus('confirmed')->sum('freeze'),
            'end_date'          => $end_date
        ]);
    }
}
