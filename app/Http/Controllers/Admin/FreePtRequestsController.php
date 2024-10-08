<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Membership;
use Auth;
use Carbon\Carbon;
use App\Models\free_pt_requests;


class FreePtRequestsController extends Controller
{
    public function index(Request $request){
    
        abort_if(Gate::denies('external_payment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employee = Auth::user()->employee;
       

        if($employee && $employee->branch_id){
            $branch_id = $employee->branch_id;
            $free_pt_requests = free_pt_requests::whereHas('lead', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->where('assigned_or_not' , '<>' , 1)
            ->get();
        }
        else{
            $free_pt_requests = free_pt_requests::where('assigned_or_not' , '<>' , 1)->get();
        }

        $trainers = User::whereRelation('roles','title','Trainer')
        ->whereHas(
            'employee',fn($q) => $q->whereStatus('active')->when(Auth()->user()->employee && Auth()->user()->employee->branch_id != NULL,fn($q) => $q->whereBranchId(Auth()->user()->employee->branch->id))
        )
        
        ->orderBy('name')
        ->pluck('name', 'id');



        return view('admin.free_pt_request.index' , compact('free_pt_requests' ,'trainers'));
    }


    public function assign_free_pt_coache(Request $request){
        $free_pt_request = free_pt_requests::findOrFail($request->id);
        $free_pt_request->assigned_or_not = 1 ;
        $free_pt_request->save();

        $membership = Membership::findOrFail($request->membership_id);
        $membership->assigned_coach_id = $request->assigned_coach ;
        $membership->assign_date = now();
        $membership->save();

        return back();


    }
    
}
