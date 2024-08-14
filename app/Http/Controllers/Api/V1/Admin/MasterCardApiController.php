<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMasterCardRequest;
use App\Http\Requests\UpdateMasterCardRequest;
use App\Http\Resources\Admin\MasterCardResource;
use App\Models\MasterCard;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MasterCardApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('master_card_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MasterCardResource(MasterCard::all());
    }

    public function store(StoreMasterCardRequest $request)
    {
        $masterCard = MasterCard::create($request->all());

        return (new MasterCardResource($masterCard))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(MasterCard $masterCard)
    {
        abort_if(Gate::denies('master_card_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MasterCardResource($masterCard);
    }

    public function update(UpdateMasterCardRequest $request, MasterCard $masterCard)
    {
        $masterCard->update($request->all());

        return (new MasterCardResource($masterCard))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(MasterCard $masterCard)
    {
        abort_if(Gate::denies('master_card_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $masterCard->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
