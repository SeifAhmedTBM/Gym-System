<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyDocumentRequest;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DocumentsController extends Controller
{
    use MediaUploadingTrait;
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('document_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employee = Auth()->user()->employee;

        if ($request->ajax()) {

            if ($employee && $employee->branch_id) 
            {
                $query = Document::with(['employee', 'created_by'])
                                        ->whereHas('employee',fn($q) => $q->whereBranchId($employee->branch_id))
                                        ->select(sprintf('%s.*', (new Document())->table));
            }else{
                $query = Document::with(['employee', 'created_by'])->select(sprintf('%s.*', (new Document())->table));
            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'document_show';
                $editGate = 'document_edit';
                $deleteGate = 'document_delete';
                $crudRoutePart = 'documents';

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
            
            $table->addColumn('employee_job_status', function ($row) {
                return $row->employee ? $row->employee->name : '';
            });

            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            
            $table->editColumn('image', function ($row) {
                if ($photo = $row->image) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });
            $table->rawColumns(['actions', 'placeholder', 'employee', 'image', 'created_by']);

            return $table->make(true);
        }

        return view('admin.documents.index');
    }

    public function create()
    {
        abort_if(Gate::denies('document_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employee = Auth()->user()->employee;
        if ($employee && $employee->branch_id != NULL) 
        {
            $employees = Employee::whereBranchId($employee->branch_id)
                                            ->whereStatus('active')
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->prepend(trans('global.pleaseSelect'), '');
        }else{
            $employees = Employee::whereStatus('active')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->prepend(trans('global.pleaseSelect'), '');
        }

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.documents.create', compact('employees', 'created_bies'));
    }

    public function store(StoreDocumentRequest $request)
    {
        $document = Document::create([
            'employee_id' => $request['employee_id'],
            'name' => $request['name'],
            'description' => $request['description'],
            'created_by_id' => Auth()->user()->id,
        ]);

        if ($request->input('image', false)) {
            $document->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $document->id]);
        }

        return redirect()->route('admin.documents.index');
    }

    public function edit(Document $document)
    {
        abort_if(Gate::denies('document_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employee = Auth()->user()->employee;
        if ($employee && $employee->branch_id != NULL) 
        {
            $employees = Employee::whereBranchId($employee->branch_id)
                                            ->whereStatus('active')
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->prepend(trans('global.pleaseSelect'), '');
        }else{
            $employees = Employee::whereStatus('active')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->prepend(trans('global.pleaseSelect'), '');
        }

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $document->load('employee', 'created_by');

        return view('admin.documents.edit', compact('employees', 'created_bies', 'document'));
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $document->update($request->all());

        if ($request->input('image', false)) {
            if (!$document->image || $request->input('image') !== $document->image->file_name) {
                if ($document->image) {
                    $document->image->delete();
                }
                $document->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($document->image) {
            $document->image->delete();
        }

        return redirect()->route('admin.documents.index');
    }

    public function show(Document $document)
    {
        abort_if(Gate::denies('document_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $document->load('employee', 'created_by');

        return view('admin.documents.show', compact('document'));
    }

    public function destroy(Document $document)
    {
        abort_if(Gate::denies('document_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $document->delete();

        return back();
    }

    public function massDestroy(MassDestroyDocumentRequest $request)
    {
        Document::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('document_create') && Gate::denies('document_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Document();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
