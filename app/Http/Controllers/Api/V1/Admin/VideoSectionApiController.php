<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVideoSectionRequest;
use App\Http\Requests\UpdateVideoSectionRequest;
use App\Http\Resources\Admin\VideoSectionResource;
use App\Models\VideoSection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VideoSectionApiController extends Controller
{
    public function index()
    {
        $video_sections = VideoSection::all();
        $response = [];

        foreach ($video_sections as $section) {
            array_push($response, [
                'video_section_name'  => $section->name,
                'videos' => $section->videos()->get()->map(function($item) { return [
                    'id'    => $item->id,
                    'url'   => $item->link,
                    'cover' => $item->images->url ?? NULL
                ]; })
            ]);
        }
        return new VideoSectionResource($response);
    }

    public function store(StoreVideoSectionRequest $request)
    {
        $videoSection = VideoSection::create($request->all());

        return (new VideoSectionResource($videoSection))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(VideoSection $videoSection)
    {
        abort_if(Gate::denies('video_section_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new VideoSectionResource($videoSection);
    }

    public function update(UpdateVideoSectionRequest $request, VideoSection $videoSection)
    {
        $videoSection->update($request->all());

        return (new VideoSectionResource($videoSection))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(VideoSection $videoSection)
    {
        abort_if(Gate::denies('video_section_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $videoSection->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
