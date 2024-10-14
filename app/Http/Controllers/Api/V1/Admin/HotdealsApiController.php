<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreHotdealRequest;
use App\Http\Requests\UpdateHotdealRequest;
use App\Http\Resources\Admin\HotdealResource;
use App\Models\Hotdeal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HotdealsApiController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        if($request->is_guest == 1 ){
            $hotdeals = Hotdeal::where('type' ,'all')->get();
        }
        else
        {
            $hotdeals = Hotdeal::where('type' ,'members')->get();
        }
       
        $response = [];
        foreach ($hotdeals as $hotdeal) {
            array_push($response, [
                'id'            => $hotdeal->id,
                'title'         => $hotdeal->title,
                'promo_code'    => $hotdeal->promo_code,
                'redeem'        => $hotdeal->redeem,   
                'type'          => $hotdeal->type,
                'description'   => $hotdeal->description,
                'created_at'    => $hotdeal->created_at,
                'cover'         => $hotdeal->cover->url ?? '',
                'logo'          => $hotdeal->logo->url ?? ''
            ]);
        }
        return new HotdealResource($response);
    }

    public function store(StoreHotdealRequest $request)
    {
        $hotdeal = Hotdeal::create($request->all());

        if ($request->input('cover', false)) {
            $hotdeal->addMedia(storage_path('tmp/uploads/' . basename($request->input('cover'))))->toMediaCollection('cover');
        }

        if ($request->input('logo', false)) {
            $hotdeal->addMedia(storage_path('tmp/uploads/' . basename($request->input('logo'))))->toMediaCollection('logo');
        }

        return (new HotdealResource($hotdeal))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Hotdeal $hotdeal)
    {
        abort_if(Gate::denies('hotdeal_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new HotdealResource($hotdeal);
    }

    public function update(UpdateHotdealRequest $request, Hotdeal $hotdeal)
    {
        $hotdeal->update($request->all());

        if ($request->input('cover', false)) {
            if (!$hotdeal->cover || $request->input('cover') !== $hotdeal->cover->file_name) {
                if ($hotdeal->cover) {
                    $hotdeal->cover->delete();
                }
                $hotdeal->addMedia(storage_path('tmp/uploads/' . basename($request->input('cover'))))->toMediaCollection('cover');
            }
        } elseif ($hotdeal->cover) {
            $hotdeal->cover->delete();
        }

        if ($request->input('logo', false)) {
            if (!$hotdeal->logo || $request->input('logo') !== $hotdeal->logo->file_name) {
                if ($hotdeal->logo) {
                    $hotdeal->logo->delete();
                }
                $hotdeal->addMedia(storage_path('tmp/uploads/' . basename($request->input('logo'))))->toMediaCollection('logo');
            }
        } elseif ($hotdeal->logo) {
            $hotdeal->logo->delete();
        }

        return (new HotdealResource($hotdeal))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Hotdeal $hotdeal)
    {
        abort_if(Gate::denies('hotdeal_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $hotdeal->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
