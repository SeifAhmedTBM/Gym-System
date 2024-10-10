<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\Admin\ServiceResource;
use App\Models\FreezeRequest;
use App\Models\Membership;
use App\Models\MembershipAttendance;
use App\Models\Service;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServicesApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('service_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ServiceResource(Service::with(['service_type', 'status'])->get());
    }

    public function store(StoreServiceRequest $request)
    {
        $service = Service::create($request->all());

        return (new ServiceResource($service))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Service $service)
    {
        abort_if(Gate::denies('service_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ServiceResource($service->load(['service_type', 'status']));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->all());

        return (new ServiceResource($service))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Service $service)
    {
        abort_if(Gate::denies('service_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $service->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
    public function takeAttend(Request $request)
    {
        if (!auth('sanctum')->check()) {
            return response()->json([
                'message' => 'Please login first!',
                'data' => null
            ], 403);
        }

        // Fetch the authenticated member
        $member = auth('sanctum')->user()->lead->load('branch');
        $setting = Setting::first();

        // branch id from membership_id
        if (isset($member->id)) {
            $membership = Membership::with(['service_pricelist' => fn($q) => $q->with('pricelist_days'), 'member'])
                ->find($request['membership_id']);

            $branch_id = $member->branch->id;

            $reminder_membership = $membership;

            if (!$membership) {
                $this->expired_membership();
                return response()->json([
                    'message' => 'Cannot find membership',
                    'data' => null
                ], 404);
            }

            if ($membership->status == 'pending') {
                $start_date = Carbon::today();

                if ($membership->service_pricelist->service->type == 'days') {
                    $end_date = $start_date->addDays(intval($membership->service_pricelist->service->expiry))->format('Y-m-d');
                } else {
                    $end_date = $start_date->addMonth(intval($membership->service_pricelist->service->expiry))->format('Y-m-d');
                }

                $membership->update([
                    'start_date' => date('Y-m-d'),
                    'end_date' => $end_date,
                    'status' => 'current'
                ]);
            }

            if ($membership->status == 'refunded') {
                return response()->json([
                    'message' => 'Membership is refunded',
                    'data' => null
                ], 404);
            }

            if (!is_null($membership)) {
                $check_last_attend = MembershipAttendance::whereMembershipId($membership->id)->get();
                if ($check_last_attend->count() > 0) {
                    $last_attend = MembershipAttendance::whereMembershipId($membership->id)->latest()->first();

                    if (date('Y-m-d', strtotime($last_attend->created_at)) == date('Y-m-d')) {
                        $diff = Carbon::parse(date('H:i:s'))->diffInMinutes(Carbon::parse($last_attend->sign_in));

                        if ($diff < 60) {
                            return response()->json([
                                'message' => 'Attended Already',
                                'data' => null
                            ], 404);
                        }
                    }
                } else {
                    $last_attend = null;
                }
            } else {
                $last_attend = null;
            }

            $freeze_request = FreezeRequest::find($request['freeze_id']);

            if ($last_attend && is_null($last_attend->sign_out)) {
                if ($last_attend->locker == $request['locker']) {
                    $last_attend->sign_out = date('H:i:s');
                    $last_attend->save();
                    return response()->json([
                        'message' => 'Sign out successfully',
                        'data' => null
                    ], 201);
                } else {
                    return response()->json([
                        'message' => 'Locker Number is not correct',
                        'data' => null
                    ], 404);
                }
            } else {
                if ($membership) {
                    if ($membership->service_pricelist->all_branches == 'true' || $membership->member->branch_id == $branch_id) {
                        $from = Carbon::parse($membership->service_pricelist->from)->format('H:i');
                        $to = Carbon::parse($membership->service_pricelist->to)->format('H:i');

                        if ($membership->service_pricelist->pricelist_days->count() <= 0 || in_array(date('D'), $membership->service_pricelist->pricelist_days()->pluck('day')->toArray())) {
                            if ($membership->service_pricelist->full_day == 'true' || $membership->service_pricelist->full_day == 'false' && $from <= date('H:i') && $to >= date('H:i')) {
                                if ($setting->has_lockers == true) {
                                    $attend = MembershipAttendance::create([
                                        'sign_in' => date('H:i:s'),
                                        'membership_id' => $membership->id,
                                        'locker' => $request['locker'],
                                        'membership_status' => $membership->status,
                                        'branch_id' => $branch_id
                                    ]);
                                } else {
                                    $attend = MembershipAttendance::create([
                                        'sign_in' => date('H:i:s'),
                                        'membership_id' => $membership->id,
                                        'locker' => $request['locker'],
                                        'membership_status' => $membership->status,
                                        'branch_id' => $branch_id
                                    ]);
                                }

                                if ($freeze_request) {
                                    if ($setting->freeze_duration == 'days') {
                                        $freeze_request_end_date = Carbon::parse($freeze_request->end_date); // end date of freeze request
                                        $now = Carbon::now()->format('Y-m-d');  // today

                                        $membership->update([
                                            'end_date' => date('Y-m-d', strtotime($membership->end_date . ' -' . $freeze_request_end_date->diffInDays($now) . ' Days'))
                                        ]);

                                        $freeze_request->update([
                                            'end_date' => date('Y-m-d', strtotime($freeze_request->end_date . ' -' . $freeze_request_end_date->diffInDays($now) . ' Days')),
                                            'freeze' => $freeze_request->freeze - $freeze_request_end_date->diffInDays($now),
                                        ]);
                                    } else {
                                        $freeze_request_end_date = Carbon::parse($freeze_request->end_date); // end date of freeze request
                                        $now = Carbon::now()->format('Y-m-d');  // today

                                        $total_freeze = $freeze_request->freeze * 7;
                                        $consumed = $total_freeze - $freeze_request_end_date->diffInDays($now);
                                        $deducted_days = ceil($consumed / 7) * 7;

                                        $membership->update([
                                            'end_date' => date('Y-m-d', strtotime($membership->end_date . ' -' . $deducted_days . ' Days'))
                                        ]);

                                        $freeze_request->update([
                                            'end_date' => date('Y-m-d', strtotime($freeze_request->end_date . ' -' . $deducted_days . ' Days')),
                                            'freeze' => ceil($consumed / 7),
                                        ]);
                                    }
                                }

                                $membership->update([
                                    'last_attendance' => $attend->created_at
                                ]);

                                $check_last_attend = MembershipAttendance::whereMembershipId($membership->id)->get();

                                if ($check_last_attend->count() == 1) {
                                    $this->welcome_call($reminder_membership);
                                }

                                return response()->json([
                                    'message' => 'Attendance recorded successfully',
                                    'data' => [
                                        'membership_id' => $membership->id,
                                        'sign_in_time' => date('H:i:s')
                                    ]
                                ], 201);
                            } else {
                                return response()->json([
                                    'message' => 'Please check the attendance time',
                                    'data' => null
                                ], 400);
                            }
                        } else {
                            return response()->json([
                                'message' => 'Please check the day for attendance',
                                'data' => null
                            ], 400);
                        }
                    } else {
                        return response()->json([
                            'message' => 'Please check branch eligibility',
                            'data' => null
                        ], 403);
                    }
                } else {
                    return response()->json([
                        'message' => 'Membership does not have main service',
                        'data' => null
                    ], 404);
                }
            }

            return response()->json([
                'message' => 'Member is not found',
                'data' => null
            ], 404);
        } else {
            return response()->json([
                'message' => 'Member is not found',
                'data' => null
            ], 404);
        }
    }
}
