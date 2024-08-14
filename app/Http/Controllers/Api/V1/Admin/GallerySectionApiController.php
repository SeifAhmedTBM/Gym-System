<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGallerySectionRequest;
use App\Http\Requests\UpdateGallerySectionRequest;
use App\Http\Resources\Admin\GallerySectionResource;
use App\Models\GallerySection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GallerySectionApiController extends Controller
{
    public function index()
    {
        $gallery = GallerySection::with(['galleries'])->get();
        $response = [];
        foreach ($gallery as $ga) {
            array_push($response, [
                'gallery_section_name' => $ga->name,
                'images' => $ga->galleries()->get()->map(function($item){ return [
                    'id'    => $item->id,
                    'url'   => $item->images->url ?? NULL
                ]; })
            ]);
        }
        return new GallerySectionResource($response);
    }

    public function store(StoreGallerySectionRequest $request)
    {
        $gallerySection = GallerySection::create($request->all());

        return (new GallerySectionResource($gallerySection))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(GallerySection $gallerySection)
    {
        abort_if(Gate::denies('gallery_section_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new GallerySectionResource($gallerySection);
    }

    public function update(UpdateGallerySectionRequest $request, GallerySection $gallerySection)
    {
        $gallerySection->update($request->all());

        return (new GallerySectionResource($gallerySection))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(GallerySection $gallerySection)
    {
        abort_if(Gate::denies('gallery_section_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $gallerySection->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
