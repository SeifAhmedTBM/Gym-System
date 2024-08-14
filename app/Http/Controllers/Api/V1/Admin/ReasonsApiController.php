<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Reason;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreReasonRequest;
use App\Http\Requests\UpdateReasonRequest;
use App\Http\Resources\Admin\ReasonResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\MediaUploadingTrait;

class ReasonsApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        // $reasons = Reason::all();

        $data = [
                [
                  'id'=>1,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r1.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>2,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r2.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>3,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r3.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>4,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r4.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>5,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r5.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>6,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r6.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>7,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r7.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>8,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r8.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>9,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r9.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>10,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r10.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>11,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r11.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>12,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r12.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>13,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r13.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>14,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r14.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
                [
                  'id'=>15,
                  'imageUrl'=>'https://soulgymegypt.com/system/public/images/r15.jpg',
                  'title'   => 'Offer 1',
                  'body'    => 'Offer Details',
                ],
        ];

        return response()->json([
            'reasons' => $data
        ]);
        // return new ReasonResource(Reason::all());
    }

    public function store(StoreReasonRequest $request)
    {
        $reason = Reason::create($request->all());

        if ($request->input('image', false)) {
            $reason->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        return (new ReasonResource($reason))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Reason $reason)
    {
        // abort_if(Gate::denies('reason_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ReasonResource($reason);
    }

    public function update(UpdateReasonRequest $request, Reason $reason)
    {
        $reason->update($request->all());

        if ($request->input('image', false)) {
            if (!$reason->image || $request->input('image') !== $reason->image->file_name) {
                if ($reason->image) {
                    $reason->image->delete();
                }
                $reason->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($reason->image) {
            $reason->image->delete();
        }

        return (new ReasonResource($reason))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Reason $reason)
    {
        abort_if(Gate::denies('reason_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $reason->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
