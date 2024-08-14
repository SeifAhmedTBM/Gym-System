<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMembershipAttendanceRequest;
use App\Http\Requests\UpdateMembershipAttendanceRequest;
use App\Http\Resources\Admin\MembershipAttendanceResource;
use App\Models\MembershipAttendance;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MembershipAttendanceApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('membership_attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MembershipAttendanceResource(MembershipAttendance::with(['locker'])->get());
    }

    public function store(StoreMembershipAttendanceRequest $request)
    {
        $membershipAttendance = MembershipAttendance::create($request->all());

        return (new MembershipAttendanceResource($membershipAttendance))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(MembershipAttendance $membershipAttendance)
    {
        abort_if(Gate::denies('membership_attendance_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MembershipAttendanceResource($membershipAttendance->load(['locker']));
    }

    public function update(UpdateMembershipAttendanceRequest $request, MembershipAttendance $membershipAttendance)
    {
        $membershipAttendance->update($request->all());

        return (new MembershipAttendanceResource($membershipAttendance))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(MembershipAttendance $membershipAttendance)
    {
        abort_if(Gate::denies('membership_attendance_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $membershipAttendance->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
