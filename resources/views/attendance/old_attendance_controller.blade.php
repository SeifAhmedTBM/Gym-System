public function getMembershipDetails(Request $request)
    {
        $master_cards = MasterCard::pluck('master_card')->toArray();

        if(in_array($request['membership_id'],$master_cards)){
            try {
                session()->flash('attended', 'master card entry');
                return back();
            } catch (\Throwable $th) {
                return back(); 
            }
        }

        try {
            $member = Lead::whereType('member')->where('member_code',$request['membership_id'])->orWhere('card_number',$request['membership_id'])->firstOr(function(){
                session()->flash('user_invalid', trans('global.member_is_not_found'));
                return back();
            }); 
            
            $memberships = Membership::with('member')->whereMemberId($member->id)->get();
            foreach($memberships as $mem){
                if($mem->status!='expired'){
                    $membership = $mem;
                    break;
                }
            }
      
            $main_group_session = null;
            $groupSessions = Membership::with('member')->whereMemberId($member->id)->orderBy('id','asc')->get();

            $group_sessions_array = [];
            foreach ($groupSessions as $key => $gs) {
                if($gs->service_pricelist->service->service_type->name == 'Group Sessions')
                {
                    array_push($group_sessions_array,$gs->id);
                }
            }

            $main_group_session = null;
            if(count($group_sessions_array) > 0){
               
                foreach($group_sessions_array as $key=> $group_session){
                    $main_group_session = Membership::find($group_sessions_array[$key]);
                    if($main_group_session->status != 'expired'){
                        break;
                    }
                }
            }
            if(!$member) {
                session()->flash('user_invalid', trans('global.member_is_not_found'));
                return back();
            }

            // if(!$membership) {
            //     return dd($membership);
            //     session()->flash('user_invalid', trans('global.membership_is_expired'));
            //     return back();
            // }

            $schedules = Schedule::with(['session','timeslot','trainer'])->where('day', date('D'))->whereHas('timeslot', function($q) {
                return $q;
            })->get();
            
            return view('attendance.show_member_details', compact('membership', 'schedules','memberships','main_group_session'));
        } catch (\Throwable $th) {
            session()->flash('user_invalid', trans('global.member_is_not_found'));
            return back();
        }
        
    }