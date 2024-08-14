<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewssectionRequest;
use App\Http\Requests\UpdateNewssectionRequest;
use App\Http\Resources\Admin\NewssectionResource;
use App\Models\Newssection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NewssectionApiController extends Controller
{
    public function index()
    {
        $news_sections = Newssection::with(['news'])->get();
        $response = [];
        foreach ($news_sections as $section) {
            array_push($response, [
                'news_section_name' => $section->name,
                'news' => $section->news()->get()->map(function($item){ return [
                    'id'        => $item->id,
                    'cover'     => $item->cover->url ?? NULL
                ]; })
            ]);
        }
        return new NewssectionResource($response);
    }

    public function store(StoreNewssectionRequest $request)
    {
        $newssection = Newssection::create($request->all());

        return (new NewssectionResource($newssection))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Newssection $newssection)
    {
        abort_if(Gate::denies('newssection_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new NewssectionResource($newssection);
    }

    public function update(UpdateNewssectionRequest $request, Newssection $newssection)
    {
        $newssection->update($request->all());

        return (new NewssectionResource($newssection))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Newssection $newssection)
    {
        abort_if(Gate::denies('newssection_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $newssection->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
