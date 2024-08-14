<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Resources\Admin\LeadResource;
use App\Models\Lead;
use App\Models\Membership;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LeadsApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new LeadResource(Lead::with(['status', 'source', 'sales_by'])->get());
    }

    public function store(StoreLeadRequest $request)
    {
        $lead = Lead::create([
            'name'      => $request['name'],
            'phone'     => $request['phone'],
            'gender'    => $request['gender'],
            'type'      => 'lead',
        ]);

        return response()->json(['message' => 'Created Successfully !'],201);
        // $lead = Lead::create($request->all());

        // if ($request->input('photo', false)) {
        //     $lead->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
        // }

        // return (new LeadResource($lead))
        //     ->response()
        //     ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Lead $lead)
    {
        abort_if(Gate::denies('lead_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new LeadResource($lead->load(['status', 'source', 'sales_by']));
    }

    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $lead->update($request->all());

        if ($request->input('photo', false)) {
            if (!$lead->photo || $request->input('photo') !== $lead->photo->file_name) {
                if ($lead->photo) {
                    $lead->photo->delete();
                }
                $lead->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }
        } elseif ($lead->photo) {
            $lead->photo->delete();
        }

        return (new LeadResource($lead))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Lead $lead)
    {
        abort_if(Gate::denies('lead_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lead->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
    
    public function checkGate()
    {
        return response()->json(true); 
    }

    public function getMemberNumber(Request $request)
    {
        if ($request->card_number == '111111') {
            return response()->json(true);
        }

        if ($request->card_number == '000000') {
            return response()->json(false);
        }

        $member = Lead::whereType('member')->whereCardNumber($request->card_number)->first();
        
        if (!$member) {
            return response()->json(false);
        }

        $membership = Membership::whereMemberId($member->id)->orderBy('id','desc')->first();

        if (!$membership) {
            return response()->json(false);
        }

        if ($membership->status == 'current') {
            return response()->json(true);
        }else{
            return response()->json(false);
        }

    }

    public function getNotifications(){
        
            $data = [];
            $item['id'] = 1;
            $item['img'] = 'https://smashgym.dotapps.net/images/smash.png';
            $item['title'] = 'Notification Title';
            $item['desctiption'] = 'Notification Description';
            $item['created_at'] = '2022-01-08';
            $item['daysago'] = "1 days ago";
            array_push($data,$item);

            return response()->json($data);
    }

    public function getReferal(){
        $data = [
            'text'=>'Invite your friends to get 15% discount from SOUL GYM! Use my promocode SOUL2021 , and show it to the FRONT DESK!! keep in touch with us on https://www.instagram.com/smashgym/',
            'promoCode'=>'Your Promo Code is SMASH'.date('Y').'',
            'imgUrl'=> "https://smashgym.dotapps.net/images/smash.png",
        ];
        return response()->json($data);
    }
}
