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

class ClassesServicesApiController extends Controller
{
    private $mobile_setting;
    public function __construct(){
        $this->mobile_setting = MobileSetting::all()->first();
    }
    public function pricelist()
    {

        $service_type_id = $this->mobile_setting->classes_service_type;

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
                    ];
                }),
            ]
        ]);

    }
    public function classes()
    {
        $service_type_id = $this->mobile_setting->classes_service_type;
        $serviceType = ServiceType::with([
            'services' => function ($query) {
                $query->with(['service_pricelist' => function ($q) {
                    $q->where('status', 'active')->whereNull('deleted_at');
                }]);
            },
         
        ])->findOrFail($service_type_id);


        $classes = $serviceType->services->map(function ($service) {
            return [
                // 'full'=> $service,
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'logo' =>  [
                    'url'   => $service->logo->url ?? NULL,
                    'thumbnail'   => $service->logo->thumbnail ?? NULL,
                    'preview'   => $service->logo->preview ?? NULL,
                ],
                'cover' =>[
                    'url'   => $service->cover->url ?? NULL,
                    'thumbnail'   => $service->cover->thumbnail ?? NULL,
                    'preview'   => $service->cover->preview ?? NULL,
                ],
                'price_list' => $service->service_pricelist->map(function ($price) {
                    return [
                        'id' => $price->id,
                        'name' => $price->name,
                        'amount' => $price->amount,
                        'session_count' => $price->session_count,
                        'max_count' => $price->max_count,
                        'from_time' => $price->from,
                        'to_time' => $price->to,
                        'all_branches' => $price->all_branches ,
                        'branches' => $price->branches(),
                        'all_days' => $price->full_day,
                        'days'=>$price->pricelist_days
                    ];
                }),
            ];
        });

        return response()->json([
            'message' => 'success',
            'data' => [
                'classes' => $classes,
            ]
        ]);
    }
    function my_classes(Request $request)  {
        if (!auth('sanctum')->check()) {
            return response()->json(['message' => 'Please login first!','data'=>null], 403);
        }
        $member = auth('sanctum')->user()->lead;

        $membership = $member->memberships()
            ->whereHas('service_pricelist.service', function ($query) {
                $query->where('service_type_id', $this->mobile_setting->classes_service_type);
            })
            ->where('status', 'current')
            ->latest()->get();
        dd($membership);
        if (!$membership) {
            return response()->json(['message' => 'Current membership is expired'], 402);
        }

    }
}
