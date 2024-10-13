<?php

namespace App\Http\Controllers\Api\V1\Admin;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\MobileSetting;
use App\Models\Setting;
use App\Models\Reminder;
use App\Models\Schedule;
use App\Models\Invitation;
use App\Models\MasterCard;
use App\Models\Membership;
use App\Models\Transaction;
use App\Models\FreeSessions;
use Illuminate\Http\Request;
use App\Models\FreezeRequest;
use App\Models\TrainerAttendant;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MembershipAttendance;
use App\Models\Source;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Payments;

class AttendanceAPIController extends Controller
{
    private $mobile_setting;
    public function __construct(){
        $this->mobile_setting = MobileSetting::all()->first();
    }

    public function getMembershipDetails(Request $request)
    {

        // member is logged in
        $member = auth('sanctum')->user()->lead;
        $setting = Setting::first()->has_lockers;
        try {
             
            $memberships = Membership::where('member_id','=',$member->id)->get();
         
            foreach($memberships as $membership){
                $this->adjustMembership($membership);
            }

            $main_membership = Membership::withCount(['invitations','free_sessions'])->whereHas('service_pricelist',function($q){
                                                $q->whereHas('service',function($x){
                                                    $x->whereHas('service_type',function($y){
                                                        $y->whereMainService(True);
                                                    });
                                                });
                                            })->whereMemberId($member->id)
                                            ->where('status','!=','expired')
                                            ->first();
            
            if ($main_membership) {
                $main_membership;
            }else{

                $main_membership = Membership::withCount(['invitations','free_sessions'])
                                    ->whereMemberId($member->id)
                                    // ->where('start_date','asc')
                                    ->first();
            }

            if(is_null($main_membership)){
                $main_membership = Membership::withCount('invitations')->whereHas('service_pricelist',function($q){
                    $q->whereHas('service',function($x){
                        $x->whereHas('service_type',function($y){
                            $y->whereMainService(True);
                        });
                    });
                })->whereMemberId($member->id)
                ->first(); 
            }
            
            $active_memberships = $member->memberships()
                ->whereNotIn('status',['expired','refunded'])
                ->orderBy('start_date','asc')->get();



            // $group_session_flag = 0;
            // $sessions_flag = 0;
            // $non_sessions_flag = 0;
            // $membership_array = [];
            // foreach($active_memberships as $active_membership){
            //     if($active_membership->service_pricelist->service->service_type->session_type == 'non_sessions'){
            //         if($non_sessions_flag == 0){
            //             array_push($membership_array,$active_membership->id);
            //             $non_sessions_flag = 1;
            //         }
            //     }elseif($active_membership->service_pricelist->service->service_type->session_type == 'group_sessions'){
            //         if($group_session_flag == 0){
            //             array_push($membership_array,$active_membership->id);
            //             $group_session_flag = 1;
            //         }
            //     }else{
            //         if($sessions_flag == 0){
            //             array_push($membership_array,$active_membership->id);
            //             $sessions_flag = 1;
            //         }
            //     }  
            // }
            // $active_memberships = Membership::whereIn('id',$membership_array)->get();

            $last_note = $member->notes()->latest()->first();

            $schedules = Schedule::with(['session','timeslot','trainer'])->where('day', date('D'))->whereHas('timeslot', function($q) {
                return $q;
            })->get();

        

            $colors = ['#2e32ac33','#ac2e2e33','#2eaca933','#37ac2e33','#ac782e33'];
            return view('attendance.show_member_details', compact('main_membership', 'schedules','member','setting','active_memberships','last_note','colors'));
        } catch (\Throwable $th) {
            // dd($th);
            session()->flash('user_invalid', trans('global.member_is_not_found'));
            return back();
        }
        
    }

    public function takeAttendance(Request $request)
    {
        $setting = Setting::first();

        $membership = Membership::with(['service_pricelist' => fn($q) => $q->with('pricelist_days')])->findOrFail($request->membership_id);

        $check_last_attend = MembershipAttendance::whereMembershipId($membership->id)->get();
        
        if(count($check_last_attend) > 0){
            $last_attend = MembershipAttendance::whereMembershipId($membership->id)->latest()->first();
        }else{
            $last_attend = null;
        }

        if ($last_attend && is_null($last_attend->sign_out)) 
        {
            if ($last_attend->locker == $request['locker']) 
            {
                $last_attend->sign_out = date('H:i:s');
                $last_attend->save();

                session()->flash('attended', 'Sign out successfully');
            }else{
                session()->flash('user_invalid', 'Locker Number is not correct !');
            }
        }else{
            if ($membership) 
            {
                $from = Carbon::parse($membership->service_pricelist->from)->format('H:i');
                $to = Carbon::parse($membership->service_pricelist->to)->format('H:i');
                
                if ($membership->service_pricelist->pricelist_days->count() <= 0 || in_array(date('D'),$membership->service_pricelist->pricelist_days()->pluck('day')->toArray())) 
                {
                    if ($membership->service_pricelist->full_day == 'true' || $membership->service_pricelist->full_day == 'false' && $from <= date('H:i') && $to >= date('H:i')) 
                    {
                        if ($setting->has_lockers == true) {
                            $attend = MembershipAttendance::create([
                                'sign_in'           => date('H:i:s'),
                                'membership_id'     => $membership->id,
                                'locker'            => $request['locker'],
                                'membership_status' => $membership->status
                            ]);
                        }else{
                            $attend = MembershipAttendance::create([
                                'sign_in'           => date('H:i:s'),
                                // 'sign_out'          => date('H:i:s'),
                                'membership_id'     => $request['membership_id'],
                                'membership_status' => $membership->status
                            ]);
                        }

                        $membership->update([
                            'last_attendance'  => $attend->created_at
                        ]);

                        if ($request->schedule_id !== null) {
                            TrainerAttendant::create([
                                'member_id'     => $request['member_id'],
                                'membership_id' => $request['membership_id'],
                                'schedule_id'   => $request['schedule_id'],
                                'trainer_id'    => $request['trainer_id']
                            ]);
                        }

                        $freeze_request = FreezeRequest::find($request['freeze_id']);
                        if ($freeze_request) {
                            if ($setting->freeze_duration == 'days') 
                            {
                                $freeze_request_end_date = Carbon::parse($freeze_request->end_date); // end date of freeze request
                                $now = Carbon::now()->format('Y-m-d');  // today 
        
                                $membership->update([
                                    'end_date'  => date('Y-m-d', strtotime($membership->end_date. ' -' . $freeze_request_end_date->diffInDays($now) . ' Days'))
                                ]);
        
                                $freeze_request->update([
                                    'end_date'  => date('Y-m-d', strtotime($freeze_request->end_date. ' -' . $freeze_request_end_date->diffInDays($now) . ' Days')),
                                    'freeze'    => $freeze_request->freeze - $freeze_request_end_date->diffInDays($now),
                                ]);
                                
                            }else{
                                $freeze_request_end_date = Carbon::parse($freeze_request->end_date); // end date of freeze request
                                $now = Carbon::now()->format('Y-m-d');  // today 
                
                                $total_freeze= $freeze_request->freeze*7;
                                $consumed = $total_freeze - $freeze_request_end_date->diffInDays($now);
                                $deducted_days = ceil($consumed/7)*7;
                                
                                $membership->update([
                                    'end_date'  => date('Y-m-d', strtotime($membership->end_date. ' -' . $deducted_days . ' Days'))
                                ]);
                            
                                $freeze_request->update([
                                    'end_date'  => date('Y-m-d', strtotime($freeze_request->end_date. ' -' . $deducted_days . ' Days')),
                                    'freeze'    => ceil($consumed/7),
                                ]);
                            }
                        }

                        

                        session()->flash('attended', trans('global.attended_successfully'));
                    }else{
                        session()->flash('wrong_time', trans('global.please_check'));
                    }
                }else{
                    session()->flash('wrong_time', trans('global.please_check_day'));
                }
            }else{
                session()->flash('membership_dont_have_main_service', trans('global.membership_dont_have_main_service'));
            }
        }

        $this->adjustMembership($membership);

        return redirect()->route('attendance_take.index');
    }

    public function fixAll()
    {
        $leads = Lead::whereType('member')->whereHas('user')->with(['user'])->get();

        foreach ($leads as $lead)
        {
            $lead->user->update([
                'phone'     => $lead->phone
            ]);
        }

        // $leads = Lead::with(['sales_reminders'])->whereDate('created_at',date('Y-m-d'))->get();

        // foreach ($leads as $key => $lead) 
        // {
        //     if ($lead->phone && $lead->phone[0] != 0) 
        //     {
        //         foreach ($lead->sales_reminders as $key => $reminder) {
        //             $reminder->delete();
        //         }
        //         $lead->delete();
        //     }
        // }

        // $invoices = Invoice::where('status','!=','refund')
        //                 ->whereHas('sales_by',fn($q) => $q->whereDoesntHave('employee'))
        //                 ->get();

        // foreach ($invoices as $key => $invoice) 
        // {
        //     $invoice->membership->update([
        //         'sales_by_id'       => $invoice->membership->member->sales_by_id
        //     ]);

        //     foreach ($invoice->payments as $key => $payment) 
        //     {
        //         $payment->update([
        //             'sales_by_id'       => $invoice->membership->member->sales_by_id
        //         ]);
        //     }

        //     $invoice->update([
        //         'sales_by_id'       => $invoice->membership->member->sales_by_id
        //     ]);
        // }
        
        return 'Success';
    }

    public function takeAttend(Request $request)
    {
        
        $setting = Setting::first();

        $member = Lead::whereType('member')
                        // ->where('id',$request['member_id'])
                        // ->orWhere('card_number',$request['card_number'])
                        ->where('member_code',$request['card_number'])
                        ->whereBranchId($request['member_branch_id'])
                        ->first();

        // dd($request->all(),$member);
        $branch_id = Auth()->user()->employee ? Auth()->user()->employee->branch_id : $request['member_branch_id'];
                    
        if (isset($member->id)) 
        {
            $membership = Membership::with(['service_pricelist' => fn($q) => $q->with('pricelist_days'),'member'])
                            ->whereMemberId($member->id)
                            ->whereHas('service_pricelist',function($q){
                                $q->whereHas('service',function($x){
                                    $x->whereHas('service_type',function($p){
                                        $p->whereMainService(true);
                                    });
                                });
                            })
                            // ->whereIn('status',['expiring','current','expired','pending'])
                            ->whereIn('status',['expiring','current','pending'])
                            // ->orderBy('id','desc')
                            ->first();

            $reminder_membership = $membership;

            if(!$membership){
            //   $membership = Membership::with(['service_pricelist' => fn($q) => $q->with('pricelist_days'),'member'])
            //                 ->whereMemberId($member->id)
            //                 ->whereHas('service_pricelist',function($q){
            //                     $q->whereHas('service',function($x){
            //                         $x->whereHas('service_type',function($p){
            //                             $p->whereMainService(true);
            //                         });
            //                     });
            //                 })
            //                 ->whereIn('status',['expiring','current','expired'])
            //                 // ->orderBy('id','desc')
            //                 ->first();
                $this->expired_membership();
                return back();
            }


            if ($membership->status == 'pending') 
            {
                $start_date = Carbon::today();

                if ($membership->service_pricelist->service->type == 'days') 
                {
                    $end_date = $start_date->addDays(intval($membership->service_pricelist->service->expiry))->format('Y-m-d');
                }else{
                    $end_date = $start_date->addMonth(intval($membership->service_pricelist->service->expiry))->format('Y-m-d');
                }

                // $this->cannotAttend();
                $membership->update([
                    'start_date'        => date('Y-m-d'),
                    'end_date'          => $end_date,
                    'status'            => 'current'
                ]);
            }

            if ($membership->status == 'refunded') 
            {
                $this->refunded_membership();
                // return 1;
                return back();
            }

            // if ($membership->status == 'expired') 
            // {
            //     $this->expired_membership();
            //     return back();
            // }

            // if(Auth::user()->id == 1){
            //     dd($membership);                                            
            // }

            if(!is_null($membership)){
                $check_last_attend = MembershipAttendance::whereMembershipId($membership->id)->get();
                if(count($check_last_attend) > 0)
                {
                    $last_attend = MembershipAttendance::whereMembershipId($membership->id)->latest()->first();

                    if (date('Y-m-d',strtotime($last_attend->created_at)) == date('Y-m-d')) 
                    {
                        $diff = Carbon::parse(date('H:i:s'))->diffInMinutes(Carbon::parse($last_attend->sign_in));
    
                        if ($diff < 60) 
                        {
                            $this->cannotAttend();
                            return back();
                        }
                    }
                }else{
                    $last_attend = null;
                }
            }else{
                $last_attend = null;
            }
          
            $freeze_request = FreezeRequest::find($request['freeze_id']);

            if ($last_attend && is_null($last_attend->sign_out)) 
            {
                if ($last_attend->locker == $request['locker']) {
                    $last_attend->sign_out = date('H:i:s');
                    $last_attend->save();
                    session()->flash('attended', 'Sign out successfully');
                }else{
                    session()->flash('user_invalid', 'Locker Number is not correct !');
                }
                
            }else{
                
                if ($membership) 
                {
                    if ($membership->service_pricelist->all_branches == 'true' || $membership->member->branch_id == $branch_id) 
                    {
                        $from = Carbon::parse($membership->service_pricelist->from)->format('H:i');
                        $to = Carbon::parse($membership->service_pricelist->to)->format('H:i');
                        
                        if ($membership->service_pricelist->pricelist_days->count() <= 0 || in_array(date('D'),$membership->service_pricelist->pricelist_days()->pluck('day')->toArray())) 
                        {
                            if ($membership->service_pricelist->full_day == 'true' || $membership->service_pricelist->full_day == 'false' && $from <= date('H:i') && $to >= date('H:i')) 
                            {
                                if ($setting->has_lockers == true) {
                                    $attend = MembershipAttendance::create([
                                        'sign_in'           => date('H:i:s'),
                                        'membership_id'     => $membership->id,
                                        'locker'            => $request['locker'],
                                        'membership_status' => $membership->status,
                                        'branch_id'         => $request['branch_id']
                                    ]);
                                }else{
                                    $attend = MembershipAttendance::create([
                                        'sign_in'           => date('H:i:s'),
                                        'membership_id'     => $membership->id,
                                        'locker'            => $request['locker'],
                                        'membership_status' => $membership->status,
                                        'branch_id'         => $request['branch_id']
                                    ]);
                                }

                                
                                if ($freeze_request) {
                                    
                                    if ($setting->freeze_duration == 'days') 
                                    {
                                        $freeze_request_end_date = Carbon::parse($freeze_request->end_date); // end date of freeze request
                                        $now = Carbon::now()->format('Y-m-d');  // today 
                                        
                                        $membership->update([
                                            'end_date'  => date('Y-m-d', strtotime($membership->end_date. ' -' . $freeze_request_end_date->diffInDays($now) . ' Days'))
                                        ]);

                                        $freeze_request->update([
                                            'end_date'  => date('Y-m-d', strtotime($freeze_request->end_date. ' -' . $freeze_request_end_date->diffInDays($now) . ' Days')),
                                            'freeze'    => $freeze_request->freeze - $freeze_request_end_date->diffInDays($now),
                                        ]);
                                        
                                    }else{
                                        $freeze_request_end_date = Carbon::parse($freeze_request->end_date); // end date of freeze request
                                        $now = Carbon::now()->format('Y-m-d');  // today 
                        
                                        $total_freeze= $freeze_request->freeze*7;
                                        $consumed = $total_freeze - $freeze_request_end_date->diffInDays($now);
                                        $deducted_days = ceil($consumed/7)*7;
                                        
                                        $membership->update([
                                            'end_date'  => date('Y-m-d', strtotime($membership->end_date. ' -' . $deducted_days . ' Days'))
                                        ]);
                                    
                                        $freeze_request->update([
                                            'end_date'  => date('Y-m-d', strtotime($freeze_request->end_date. ' -' . $deducted_days . ' Days')),
                                            'freeze'    => ceil($consumed/7),
                                        ]);
                                    }
                                }

                                $membership->update([
                                    'last_attendance'      => $attend->created_at
                                ]);

                                session()->flash('attended', trans('global.attended_successfully'));

                                $check_last_attend = MembershipAttendance::whereMembershipId($membership->id)->get();
                            
                                if(count($check_last_attend) == 1)
                                {
                                    $this->welcome_call($reminder_membership);
                                }

                            }else{
                                session()->flash('wrong_time', trans('global.please_check'));
                            }
                        }else{
                            session()->flash('wrong_time', trans('global.please_check_day'));
                        }  
                    }else{
                        session()->flash('cannot_attend', trans('global.please_check_branch'));
                    }
                        
                }else{
                    session()->flash('membership_dont_have_main_service', trans('global.membership_dont_have_main_service'));
                }
            }
            
            return redirect()->route('admin.members.show',$member->id);
        }else{
            session()->flash('user_invalid', trans('global.member_is_not_found'));
            // return 3;
            return back();
        }
    }

    public function checkFreeze($membership_id)
    {
        $freeze = FreezeRequest::with(['membership' => fn($q) => $q->with('service_pricelist')])
                                        ->whereMembershipId($membership_id)
                                        ->whereDate('start_date','<=',date('Y-m-d'))
                                        ->whereDate('end_date','>',date('Y-m-d'))
                                        ->whereStatus('confirmed')
                                        ->first();
        
        $attend = MembershipAttendance::whereMembershipId($membership_id)->latest()->first();

        return response()->json([
            'freeze'    => $freeze,
            'attend'    => $attend
        ]);
    }

    public function takeFreezeAttend(Request $request)
    {
        $membership = Membership::with([
                                    'service_pricelist' => fn($q) => $q->with('pricelist_days')
                                ])->findOrFail($request->membership_id);

        $freeze_request = FreezeRequest::find($request['freeze_id']);
        $setting = Setting::first();

        $from = Carbon::parse($membership->service_pricelist->from)->format('H:i');
        $to = Carbon::parse($membership->service_pricelist->to)->format('H:i');

        if ($membership->service_pricelist->pricelist_days->count() <= 0 || in_array(date('D'),$membership->service_pricelist->pricelist_days()->pluck('day')->toArray())) 
        {
            if ($membership->service_pricelist->full_day == 'true' || $membership->service_pricelist->full_day == 'false' && $from <= date('H:i')) 
            {
                
                if ($setting->has_lockers == true) {
                    $attend = MembershipAttendance::create([
                        'sign_in'       => date('H:i:s'),
                        'membership_id' => $membership->id,
                        'membership_status' => $membership->status
                    ]);
                }else{
                    $attend = MembershipAttendance::create([
                        'sign_in'       => date('H:i:s'),
                        // 'sign_out'       => date('H:i:s'),
                        'membership_id' => $request['membership_id'],
                        'membership_status' => $membership->status
                    ]);
                }
                
        
                if ($request->schedule_id !== null) 
                {
                    TrainerAttendant::create([
                        'member_id'     => $request['member_id'],
                        'schedule_id'   => $request['schedule_id'],
                        'membership_id' => $request['membership_id'],
                        'trainer_id'    => $request['trainer_id']
                    ]);
                }

                $membership->update([
                    'last_attendance'  => $attend->created_at
                ]);

                if ($freeze_request) {
                    if ($setting->freeze_duration == 'days') 
                    {
                        $freeze_request_end_date = Carbon::parse($freeze_request->end_date); // end date of freeze request
                        $now = Carbon::now()->format('Y-m-d');  // today 

                        $membership->update([
                            'end_date'  => date('Y-m-d', strtotime($membership->end_date. ' -' . $freeze_request_end_date->diffInDays($now) . ' Days'))
                        ]);

                        $freeze_request->update([
                            'end_date'  => date('Y-m-d', strtotime($freeze_request->end_date. ' -' . $freeze_request_end_date->diffInDays($now) . ' Days')),
                            'freeze'    => $freeze_request->freeze - $freeze_request_end_date->diffInDays($now),
                        ]);
                        
                    }elseif($setting->freeze_duration == 'weeks'){
                        $freeze_request_end_date = Carbon::parse($freeze_request->end_date); // end date of freeze request
                        $now = Carbon::now()->format('Y-m-d');  // today 

                        $total_freeze= $freeze_request->freeze*7;
                        $consumed = $total_freeze - $freeze_request_end_date->diffInDays($now);
                        $deducted_days = ceil($consumed/7)*7;
                        
                        $membership->update([
                            'end_date'  => date('Y-m-d', strtotime($membership->end_date. ' -' . $deducted_days . ' Days'))
                        ]);
                    
                        $freeze_request->update([
                            'end_date'  => date('Y-m-d', strtotime($freeze_request->end_date. ' -' . $deducted_days . ' Days')),
                            'freeze'    => ceil($consumed/7),
                        ]);
                    }
                }

                
                session()->flash('attended', trans('global.attended_successfully'));

                $this->adjustMembership($membership);

                return redirect()->route('attendance_take.index');
                
            }else{
                session()->flash('wrong_time', trans('global.please_check'));
                return redirect()->route('attendance_take.index');
            }
        }else{
            session()->flash('wrong_time', trans('global.please_check_day'));
        }
    }

    public function invitation(Request $request)
    {
        $membership = Membership::whereHas('member')->with('member')->whereId($request->membership_id)->first();
        $sales_by_id = $membership->member->sales_by_id;
        if (is_null($request['lead_id'])) {
                $request->validate([
                    'name'                  => 'required',
                    'phone'                 => 'min:11|max:11|unique:leads,phone',
                    'gender'                => 'required',
                    'branch_id'             => 'required',
                    // 'sales_by_id'           => 'required',
                ]);

                $lead = Lead::create([
                    'name'              => $request['name'],
                    'phone'             => $request['phone'],
                    'sales_by_id'       => $sales_by_id,
                    'branch_id'         => $request['branch_id'],
                    'gender'            => $request['gender'],
                    'type'              => 'lead',
                    'source_id'         => Source::whereName('invitation')->first()->id ?? Source::firstOrCreate(['name' => 'invitation'])->id,
                ]);

                $invitation = Invitation::create([
                    'member_id'         => $membership->member->id,
                    'lead_id'           => $lead->id,
                    'membership_id'     => $request['membership_id']
                ]);
        }else{
            $invitation = Invitation::create([
                'member_id'             => $membership->member->id,
                'lead_id'               => $request['lead_id'],
                'membership_id'         => $request['membership_id']
            ]);
        }

        $reminder = Reminder::create([
            'type'              => 'sales',
            'lead_id'           => $lead->id,
            'due_date'          => $request['followup'],
            'user_id'           => $sales_by_id,
        ]);

        session()->flash('invitation', 'Invitation Created successfully !');
        return back();
    }

    public function deleteInvitation($id)
    {
        $invitation = Invitation::findOrFail($id)->delete();

        $this->deleted();
        return back();
    }

    public function freeSession(Request $request)
    {
        $membership = Membership::whereHas('member')->with(['member','service_pricelist','free_sessions'])
                                    ->withCount('free_sessions')
                                    ->whereId($request->membership_id)
                                    ->first();

        if ($membership->service_pricelist->free_sessions >= $membership->free_sessions_count) 
        {
            $free_session = FreeSessions::create([
                'membership_id'     => $membership->id,
                'notes'             => $request['notes']
            ]);

            $this->sent_successfully();
        }else{
            $this->something_wrong();
        }

        
        return back();
    }

    public function takeManualAttend(Request $request)
    {
     
        $user_id = $request->user()->id;

        $Lead = Lead::where('user_id' , $user_id)->latest()->first();
    
        $membership = Membership::where('member_id', $Lead->id)
        ->whereHas('service_pricelist', function ($q) {
            $q->whereHas('service', function ($x) {
                $x->whereHas('service_type', function ($i) {
                    $i->where([
                        ['isClass', false],
                        ['is_pt', false],
                    ]); // Ensure this is the correct column name
                });
            });
        })
        ->with('attendances')
        ->latest()
        ->first();
      
       

        $last_attend    = $membership->attendances()->whereDate('created_at',date('Y-m-d'))->latest()->first();

        if (isset($last_attend) && $last_attend) 
        {
            $attend = MembershipAttendance::create([
                'sign_in'                   => $request['time'],
                'sign_out'                  => $request['time'],
                'membership_id'             => $membership->id,
                //'created_at'                => $request['date'].$request['time'],
                'membership_status' => $membership->status
            ]);
        }else{
            $attend = MembershipAttendance::create([
                'sign_in'                   => $request['time'],
                'sign_out'                  => $request['time'],
                'membership_id'             => $membership->id,
                //'created_at'                => $request['date'].$request['time'],
                'membership_status'         => $membership->status
            ]);
        }

        if (isset($attend)) 
        {
            $membership->update([
                'last_attendance'      => $attend->created_at
            ]);
        }

        return response()->json([
            'status'                     =>  true ,
            'message'                    =>  "attended successfully"
        ],200);

        // $this->sent_successfully();
        // return back();
    }


    public function takePtAttend(Request $request)
    {
     
        $user_id = $request->user()->id;

        $Lead = Lead::where('user_id' , $user_id)->latest()->first();
        $main_membership = Membership::where('member_id', $Lead->id)
        ->whereHas('service_pricelist', function ($q) {
            $q->whereHas('service', function ($x) {
                $x->whereHas('service_type', function ($i) {
                    $i->where([
                        ['isClass', false],
                        ['is_pt', false],
                    ]); // Ensure this is the correct column name
                });
            });
        })
        ->with('attendances')
        ->latest()
        ->first();
      
        $pt_membership = Membership::where('member_id', $Lead->id)
        ->whereHas('service_pricelist', function ($q) {
            $q->whereHas('service', function ($x) {
                $x->whereHas('service_type', function ($i) {
                    $i->where([
                        ['isClass', false],
                        ['is_pt', true],
                    ]); // Ensure this is the correct column name
                });
            });
        })
        ->with('attendances')
        ->latest()
        ->first();
      
       
        $current_time = now();
        $last_pt_attend                     = $pt_membership->attendances()->whereDate('created_at',date('Y-m-d'))->latest()->first();
        $last_main_membership_attendnace    = $main_membership->attendances()->whereDate('created_at',date('Y-m-d'))->latest()->first();

        
     
        if($last_main_membership_attendnace)
        {
            if ($last_pt_attend) 
            {
                $last_pt_created_at = $last_pt_attend->created_at;
                if($last_pt_created_at->lt($current_time->subHours(12))){
                    $attend = MembershipAttendance::create([
                        'sign_in'                   => $request['time'],
                        'sign_out'                  => $request['time'],
                        'membership_id'             => $pt_membership->id,
                        //'created_at'                => $request['date'].$request['time'],
                        'membership_status'         => $pt_membership->status
                    ]); 
                }
               
                else
                {
                    return response()->json([
                        'status'                     => false ,
                        'message'                    =>  "You are signed in less than 12 hours ago."
                    ],422);
                }
            }
            else
            {
                $attend = MembershipAttendance::create([
                    'sign_in'                   => $request['time'],
                    'sign_out'                  => $request['time'],
                    'membership_id'             => $pt_membership->id,
                    //'created_at'                => $request['date'].$request['time'],
                    'membership_status'         => $pt_membership->status
                ]);
             
            }
        }
        else
        {
            return response()->json([
                'status'                     => false ,
                'message'                    =>  "You Must Attend In Gym Firstly"
            ],422);
        }
        if (isset($attend)) 
        {
            $pt_membership->update([
                'last_attendance'      => $attend->created_at
            ]);
        }

        return response()->json([
            'status'                     =>  true ,
            'message'                    =>  "You Attend Succefully"
        ],200);

     
    }
}
