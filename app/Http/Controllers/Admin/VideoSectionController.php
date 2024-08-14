<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyVideoSectionRequest;
use App\Http\Requests\StoreVideoSectionRequest;
use App\Http\Requests\UpdateVideoSectionRequest;
use App\Models\VideoSection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class VideoSectionController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('video_section_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = VideoSection::query()->select(sprintf('%s.*', (new VideoSection())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'video_section_show';
                $editGate = 'video_section_edit';
                $deleteGate = 'video_section_delete';
                $crudRoutePart = 'video-sections';

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

        return view('admin.videoSections.index');
    }

    public function create()
    {
        abort_if(Gate::denies('video_section_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.videoSections.create');
    }

    public function store(StoreVideoSectionRequest $request)
    {
        $videoSection = VideoSection::create($request->all());

        return redirect()->route('admin.video-sections.index');
    }

    public function edit(VideoSection $videoSection)
    {
        abort_if(Gate::denies('video_section_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.videoSections.edit', compact('videoSection'));
    }

    public function update(UpdateVideoSectionRequest $request, VideoSection $videoSection)
    {
        $videoSection->update($request->all());

        return redirect()->route('admin.video-sections.index');
    }

    public function show(VideoSection $videoSection)
    {
        abort_if(Gate::denies('video_section_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.videoSections.show', compact('videoSection'));
    }

    public function destroy(VideoSection $videoSection)
    {
        abort_if(Gate::denies('video_section_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $videoSection->delete();

        return back();
    }

    public function massDestroy(MassDestroyVideoSectionRequest $request)
    {
        VideoSection::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
