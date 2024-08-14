<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyGallerySectionRequest;
use App\Http\Requests\StoreGallerySectionRequest;
use App\Http\Requests\UpdateGallerySectionRequest;
use App\Models\GallerySection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class GallerySectionController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('gallery_section_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = GallerySection::query()->select(sprintf('%s.*', (new GallerySection())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'gallery_section_show';
                $editGate = 'gallery_section_edit';
                $deleteGate = 'gallery_section_delete';
                $crudRoutePart = 'gallery-sections';

                return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.gallerySections.index');
    }

    public function create()
    {
        abort_if(Gate::denies('gallery_section_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.gallerySections.create');
    }

    public function store(StoreGallerySectionRequest $request)
    {
        $gallerySection = GallerySection::create($request->all());

        return redirect()->route('admin.gallery-sections.index');
    }

    public function edit(GallerySection $gallerySection)
    {
        abort_if(Gate::denies('gallery_section_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.gallerySections.edit', compact('gallerySection'));
    }

    public function update(UpdateGallerySectionRequest $request, GallerySection $gallerySection)
    {
        $gallerySection->update($request->all());

        return redirect()->route('admin.gallery-sections.index');
    }

    public function show(GallerySection $gallerySection)
    {
        abort_if(Gate::denies('gallery_section_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.gallerySections.show', compact('gallerySection'));
    }

    public function destroy(GallerySection $gallerySection)
    {
        abort_if(Gate::denies('gallery_section_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $gallerySection->delete();

        return back();
    }

    public function massDestroy(MassDestroyGallerySectionRequest $request)
    {
        GallerySection::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
