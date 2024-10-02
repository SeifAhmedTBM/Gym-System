<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\Admin\ServiceResource;
use App\Models\MobileSetting;
use App\Models\Pricelist;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PTServicesApiController extends Controller
{
    private $setting;
    public function __construct(){
        $this->setting = MobileSetting::all()->first();
    }
    public function pricelist()
    {
//        abort_if(Gate::denies('service_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
//        return new ServiceResource(Service::with(['service_type', 'status'])->get());
        $service_type_id = MobileSetting::first()->pt_service_type;
        $service_type = ServiceType::with(['service_pricelists' => fn($q) => $q->where('pricelists.status','active')])->findOrFail($service_type_id);
        return response()->json([
            'message'=>"success",
            'data'=>[
                'service_type'      => $service_type->makeHidden('service_pricelists'),
                'pricelists'        => $service_type->service_pricelists->map(function($price){
                    return [
                        'id'=>$price->id,
                        'amount'=>$price->amount,
                        'session_count'=>$price->session_count,
                        'name'=>$price->name,
//                        'name'=>$price->name,
                    ];
                }),
            ]
        ]);

    }
    public function trainers(){
        $trainers = User::with(['employee', 'employee.branch'])
            ->whereHas('roles', function ($q) {
                $q->where('title', 'Trainer');
            })
            ->whereHas('employee', function ($i) {
                $i->whereStatus('active')->where('mobile_visibility', true);
            })
            ->get()
            ->map(function ($trainer) {
                return [
                    'id' => $trainer->id,
                    'name' => $trainer->employee->name,
                    'profile_photo' => $trainer->employee->profile_photo,
                    'branch_name' => $trainer->employee->branch->name ?? '-', // Fallback if branch is not available
                ];
            });

        return response()->json([
            'message'=>"success",
            'data'=>[
                'trainers'      => $trainers,
            ]
        ]);

    }
    public function trainers_pricelist(Request $request){
        if (!auth('sanctum')->check()) {
            return response()->json([
                'message' => 'Please login first!',
                'data' => null
            ], 403);
        }
        $member = auth('sanctum')->user()->lead;

        $service_type_id = MobileSetting::first()->pt_service_type;
        $service_type = ServiceType::with(['service_pricelists' => fn($q) => $q->where('pricelists.status','active')])->findOrFail($service_type_id);
        $trainers = User::with(['employee', 'employee.branch'])
            ->whereHas('roles', function ($q) {
                $q->where('title', 'Trainer');
            })
            ->whereHas('employee', function ($b) use ($member) {
                 // all_branches
                $b->where('branch_id', $member->branch->id);
            })
            ->whereHas('employee', function ($i) {
                $i->whereStatus('active')->where('mobile_visibility', true);
            })
            ->get()
            ->map(function ($trainer) use ($service_type) {
                return [
                    'id' => $trainer->id,
                    'name' => $trainer->employee->name,
                    'profile_photo' => $trainer->employee->profile_photo,
                    'branch_name' => $trainer->employee->branch->name ?? '-', // Fallback if branch is not available
                    'service_id'=>$service_type->id,
                    'service_name'=>$service_type->name,
                    'price_list'=>$service_type->service_pricelists->map(function($price) use ($service_type,$trainer){
                        return [
                            'id'=>$price->id,
                            'amount'=>$price->amount,
                            'session_count'=>$price->session_count,
                            'name'=>$price->name,
                            'branches' =>[[
                                'id'=>$trainer->employee->branch->id,
                                'name'=> $trainer->employee->branch->name,
                            ]],
                        ];
                    }),
                ];
            });

        return response()->json([
            'message'=>"success",
            'data'=>[
                'trainers' => $trainers,
            ]
        ]);

    }
}
