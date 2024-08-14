<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Http\Request;
use App\Models\MemberRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\MemberRequestReplies;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use App\Exports\ExportMemberRequestSheet;
use App\Http\Requests\StoreMemberRequestReply;
use App\Http\Requests\StoreMemberRequestRequest;

class MemberRequestsController extends Controller
{
    public function index(Request $request)
    {
        $member_requests = MemberRequest::index($request->all())->whereHas('member')->latest()->paginate(50);

        return view('admin.memberRequests.index', compact('member_requests'));
    }

    public function storeReply(StoreMemberRequestReply $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $member_reply = MemberRequestReplies::create($data);
        $this->created();
        return back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMemberRequestRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $member_request = MemberRequest::create($data);

        Alert::success(NULL, trans('global.request_of_created_successfully', ['member' => $member_request->member->name]));
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(MemberRequest $member_request)
    {
        return view('admin.memberRequests.show')->with(['member_request' => $member_request]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(MemberRequest $member_request)
    {
        return view('admin.memberRequests.edit')->with(['member_request' => $member_request]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MemberRequest $member_request)
    {
        // return $request->all();
        // $request->validate([
        //     'status'    => 'bail|required|in:approved,rejected'
        // ]);

        $member_request->update([
            'subject'           => $request['subject'],
            'comment'           => $request['comment'],
            'created_at'        => $request['date'].$request['time'],
        ]);

        $this->updated();

        return redirect()->route('admin.member-requests.index');
    }

    public function updateStatus(Request $request, MemberRequest $member_request)
    {
        $request->validate([
            'status'    => 'bail|required|in:approved,rejected'
        ]);

        $member_request->update([
            'status'    => $request['status']
        ]);

        ////////////// Alerts
        $user_alert = UserAlert::create([
            'alert_text'        => 'Member request #'.$member_request->id.' Has Been '.$member_request->status,
            'alert_link'        => route('admin.member-requests.show',$member_request->id),
        ]);
        
        $admins = User::whereHas('roles', function($q) {
            $q = $q->whereIn('title', ['Developer','Sales','Receptionist']);
        })->pluck('name', 'id');

        foreach($admins as $id => $admin) 
        {
            DB::table('user_user_alert')->insert(['user_alert_id' => $user_alert->id, 'user_id' => $id, 'read' => 0]);
        }
        //////////////
        
        $this->updated();
        return back();
    }

    public function destroy($id)
    {
        $member_request = MemberRequest::findOrFail($id);
        $member_request->delete();

        $this->deleted();
        return back();
    }

    public function exportMemberRequestSheet(Request $request)
    {
        return Excel::download(new ExportMemberRequestSheet($request->all()), 'member_requests.xlsx');
    }
}
