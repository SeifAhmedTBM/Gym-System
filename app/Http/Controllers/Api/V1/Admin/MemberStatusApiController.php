<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberStatusRequest;
use App\Http\Requests\UpdateMemberStatusRequest;
use App\Http\Resources\Admin\MemberStatusResource;
use App\Models\MemberStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MemberStatusApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('member_status_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MemberStatusResource(MemberStatus::all());
    }

    public function store(StoreMemberStatusRequest $request)
    {
        $memberStatus = MemberStatus::create($request->all());

        return (new MemberStatusResource($memberStatus))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(MemberStatus $memberStatus)
    {
        abort_if(Gate::denies('member_status_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MemberStatusResource($memberStatus);
    }

    public function update(UpdateMemberStatusRequest $request, MemberStatus $memberStatus)
    {
        $memberStatus->update($request->all());

        return (new MemberStatusResource($memberStatus))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(MemberStatus $memberStatus)
    {
        abort_if(Gate::denies('member_status_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $memberStatus->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
