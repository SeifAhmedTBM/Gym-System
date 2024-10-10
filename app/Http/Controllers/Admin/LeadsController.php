<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lead;
use App\Models\Note;
use App\Models\User;
use App\Models\Branch;
use App\Models\Source;
use App\Models\Status;
use App\Models\Address;
use App\Models\Setting;
use App\Models\Reminder;
use App\Models\Membership;
use App\Exports\LeadsExport;
use App\Imports\LeadsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\LeadRemindersHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\MassDestroyLeadRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\Sport;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class LeadsController extends Controller
{
    use MediaUploadingTrait;
    use CsvImportTrait;
    public function index(Request $request)
    {
        abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);

        $employee = Auth()->user()->employee;

        $user = Auth()->user();

        $selected_branch = Auth()->user()->employee->branch ?? NULL;

        if ($request->ajax()) {

            if ($employee && $employee->branch_id != NULL) {
                if ($user->roles[0]->title == 'Sales') {
                    $query = Lead::index($data)
                        ->with(['status', 'source', 'sales_by', 'address', 'branch', 'sport'])
                        ->whereType('lead')
                        ->whereBranchId($employee->branch_id)
                        ->whereSalesById($user->id)
                        ->orderBy('id', 'desc')
                        ->select(sprintf('%s.*', (new Lead())->table));
                } else {
                    $query = Lead::index($data)
                        ->with(['status', 'source', 'sales_by', 'address', 'branch', 'sport'])
                        ->whereType('lead')
                        ->whereBranchId($employee->branch_id)
                        ->orderBy('id', 'desc')
                        ->select(sprintf('%s.*', (new Lead())->table));
                }
            } else {
                $query = Lead::index($data)
                    ->with(['status', 'source', 'sales_by', 'address', 'branch', 'sport'])
                    ->whereType('lead')
                    ->orderBy('id', 'desc')
                    ->select(sprintf('%s.*', (new Lead())->table));
            }

            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'lead_show';
                $editGate = 'lead_edit';
                $deleteGate = 'lead_delete';
                $crudRoutePart = 'leads';

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

            $table->editColumn('photo', function ($row) {
                if ($photo = $row->photo) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                } else {
                    $setting = Setting::first()->menu_logo;

                    return '<a href="' . route('admin.leads.show', $row->id) . '" target="_blank">
                    <img src="' . asset('images/' . $setting) . '" width="50px" height="50px">
                    </a>';
                }
            });

            $table->editColumn('name', function ($row) {
                return $row->name ? '<a href="' . route('admin.leads.show', $row->id) . '" target="_blank">' . $row->name . '</a> 
                <br> <a href="' . route('admin.leads.show', $row->id) . '" target="_blank">' . $row->phone . '</a>' : '';
            });


            $table->editColumn('national', function ($row) {
                return $row->national ? $row->national : '';
            });

            $table->editColumn('member_code', function ($row) {
                return $row->member_code ? $row->member_code : '';
            });

            $table->addColumn('address_name', function ($row) {
                return $row->address ? $row->address->name : '';
            });

            $table->addColumn('branch_name', function ($row) {
                return $row->branch ? $row->branch->name : '';
            });

            $table->addColumn('parent', function ($row) {
                return $row->parent_phone ? $row->parent_phone . '<br>' . $row->parent_phone_two : '';
            });

            $table->addColumn('status_name', function ($row) {
                return $row->status ? $row->status->name : '';
            });

            $table->addColumn('source_name', function ($row) {
                return $row->source ? $row->source->name : '';
            });

            $table->addColumn('sport_name', function ($row) {
                return $row->sport ? $row->sport->name : '';
            });

            $table->editColumn('gender', function ($row) {
                return $row->gender ? Lead::GENDER_SELECT[$row->gender] : '';
            });

            $table->addColumn('sales_by_name', function ($row) {
                return $row->sales_by ? $row->sales_by->name : '';
            });

            $table->addColumn('type', function ($row) {
                return $row->type ? Lead::TYPE_SELECT[$row->type] : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'name', 'phone', 'placeholder', 'photo', 'status', 'source', 'downloaded_app', 'sales_by', 'branch_name', 'parent']);

            return $table->make(true);
        }

        $branches = Branch::pluck('name', 'id');

        $statuses = Status::pluck('name', 'id');

        $sources = Source::pluck('name', 'id');

        $addresses = Address::pluck('name', 'id');

        $sports = Sport::pluck('name', 'id');

        $sales = User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->pluck('name', 'id');

        if ($employee && $employee->branch_id != NULL) {
            if ($user->roles[0]->title == 'Sales') {
                $leads = Lead::index($data)->whereType('lead')->whereSalesById(Auth()->id())->whereBranchId($employee->branch_id)->count();
            } else {
                $leads = Lead::index($data)->whereType('lead')->whereBranchId($employee->branch_id)->count();
            }
        } else {
            $leads = Lead::index($data)->whereType('lead')->count();
        }

        return view('admin.leads.index', compact('statuses', 'sales', 'sources', 'addresses', 'leads', 'branches', 'sports'));
    }

    public function create()
    {
        abort_if(Gate::denies('lead_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $statuses = Status::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sources = Source::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $addresses = Address::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $trainers = User::whereRelation('roles', 'title', 'Trainer')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $selectedBranch = isset(Auth::user()->employee) ? Auth::user()->employee->branch_id : NULL;

        $sales_bies = User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->whereHas('employee', function ($i) {
            $i->whereStatus('active');
        });

        if ($selectedBranch) {
            $sales_bies->whereHas('employee', fn($i) =>
            $i->whereHas('branch', fn($x) =>
            $x->where('id', $selectedBranch)));
        }

        $sales_bies = $sales_bies->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sports = Sport::pluck('name', 'id');

        return view('admin.leads.create', compact('statuses', 'sources', 'sales_bies', 'addresses', 'branches', 'selectedBranch', 'sports', 'trainers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                 => 'string|required',
            'phone'                => 'string|required_unless:minor,yes|min:11|max:11|unique:leads,phone,NULL,id,deleted_at,NULL',
            'source_id'            => 'required',
            'gender'               => 'required',
            'sales_by_id'          => 'required',
            'trainer_id'           => 'required_if:invitation,true',
        ]);

        DB::transaction(function () use ($request) {
            $selected_branch = Auth()->user()->employee->branch ?? NULL;

            $lead = Lead::create([
                'name'              => $request['name'],
                'phone'             => isset($request->minor) ? '0x' . random_int(100000000, 999999999) : $request['phone'],
                'national'          => $request['national'],
                'status_id'         => Status::first()->id,
                'source_id'         => $request['source_id'],
                'address_id'        => $request['address_id'],
                'dob'               => $request['dob'],
                'gender'            => $request['gender'],
                'sales_by_id'       => $request['sales_by_id'],
                'type'              => 'lead',
                'referral_member'   => $request['referral_member'],
                'address_details'   => $request['address_details'],
                'whatsapp_number'   => $request['whatsapp_number'],
                'branch_id'         => $selected_branch->id ?? Branch::first()->id,
                'sport_id'          => $request['sport_id'] ?? NULL,
                'notes'             => $request['notes'],
                'created_by_id'     => Auth()->user()->id,
                'invitation'        => isset($request['invitation']) ? true : false,
                'trainer_id'        => isset($request['invitation']) == 'true' ? $request['trainer_id'] : NULL,
                'parent_phone'      => isset($request->minor) ? $request['parent_phone'] : null,
                'parent_phone_two'  => isset($request->minor) ? $request['parent_phone_two'] : null,
                'parent_details'    => isset($request->minor) ? $request['parent_details'] : null,
                'medical_background'    => $request['medical_background']
            ]);

            // reminder
            $reminder = Reminder::create([
                'type'              => 'sales',
                'lead_id'           => $lead->id,
                'due_date'          => $request->followup,
                'user_id'           => $request->sales_by_id,
            ]);

            if ($request->input('photo', false)) {
                $lead->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }

            if ($media = $request->input('ck-media', false)) {
                Media::whereIn('id', $media)->update(['model_id' => $lead->id]);
            }

            $this->sent_successfully();
        });

        return redirect()->route('admin.leads.index');
    }

    public function edit(Lead $lead)
    {
        abort_if(Gate::denies('lead_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $statuses = Status::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sources = Source::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $addresses = Address::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $trainers = User::whereRelation('roles', 'title', 'Trainer')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sales_bies = User::whereHas('roles', function ($q) {
            $q->where('title', 'Sales');
        })->whereHas('employee', function ($i) {
            $i->whereStatus('active');
        })->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sports = Sport::pluck('name', 'id');

        $lead->load('status', 'source', 'sales_by', 'sport');

        return view('admin.leads.edit', compact('statuses', 'sources', 'sales_bies', 'lead', 'addresses', 'branches', 'sports', 'trainers'));
    }

    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            "name"                 => "string|required",
            "phone"                => "required_unless:minor,yes|min:11|max:11|unique:leads,phone,$lead->id",
            // "national"             => "string|required_unless:minor,yes|min:14|max:14|unique:leads,national,$lead->id",
            // "national"             => "nullable|min:14|max:14|unique:leads,national,$lead->id",
            "branch_id"            => "required",
            "source_id"            => "required",
            "gender"               => "required",
            "sales_by_id"          => "required",
            "trainer_id"           => "required_if:invitation,true",
        ]);

        $lead->update([
            'name'              => $request['name'],
            'phone'             => isset($request->minor) ? '0x' . random_int(100000000, 999999999) : $request['phone'],
            'national'          => $request['national'],
            'status_id'         => $request['status_id'],
            'source_id'         => $request['source_id'],
            'address_id'        => $request['address_id'],
            'dob'               => $request['dob'],
            'gender'            => $request['gender'],
            'sales_by_id'       => $request['sales_by_id'],
            'type'              => 'lead',
            'referral_member'   => $request['referral_member'],
            'address_details'   => $request['address_details'],
            'whatsapp_number'   => $request['whatsapp_number'],
            'notes'             => $request['notes'],
            'branch_id'         => $request['branch_id'],
            'sport_id'          => $request['sport_id'],
            'invitation'        => isset($request['invitation']) ? true : false,
            'trainer_id'        => isset($request['invitation']) == 'true' ? $request['trainer_id'] : NULL,
            'parent_phone'      => isset($request->minor) ? $request['parent_phone'] : null,
            'parent_phone_two'  => isset($request->minor) ? $request['parent_phone_two'] : null,
            'parent_details'    => isset($request->minor) ? $request['parent_details'] : null,
            'medical_background'    => $request['medical_background']
        ]);

        if ($request->input('photo', false)) {
            if (!$lead->photo || $request->input('photo') !== $lead->photo->file_name) {
                if ($lead->photo) {
                    $lead->photo->delete();
                }
                $lead->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }
        } elseif ($lead->photo) {
            $lead->photo->delete();
        }

        $this->updated();
        return redirect()->route('admin.leads.index');
    }

    public function show(Lead $lead)
    {
        abort_if(Gate::denies('lead_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lead->load(['status', 'source', 'sales_by', 'address', 'reminderHistory' => fn ($q) => $q->latest()]);

        $statuses = Status::pluck('name', 'id');

        return view('admin.leads.show', compact('lead', 'statuses'));
    }

    public function destroy(Lead $lead)
    {
        abort_if(Gate::denies('lead_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $reminers = Reminder::where('lead_id', '=', $lead->id)->forceDelete();
        $lead->forceDelete();

        return back();
    }

    public function massDestroy(MassDestroyLeadRequest $request)
    {
        Lead::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('lead_create') && Gate::denies('lead_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Lead();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function import(Request $request)
    {
        Excel::import(new LeadsImport, $request->upload);
        $this->created();

        return back();
    }

    public function referralMember(Request $request)
    {
        $member = Lead::whereType('member')->whereMemberCode($request->referral_member)->firstOrFail();

        return response()->json(['member' => $member]);
    }

    public function export(Request $request)
    {
        return Excel::download(new LeadsExport($request), 'Leads.xlsx');
    }

    public function addNote($id)
    {
        $lead = Lead::with(['Notes' => fn ($q) => $q->latest()])->findOrFail($id);

        return view('admin.leads.notes', compact('lead'));
    }

    public function addNewNote(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $note = Note::create([
            'lead_id'               => $lead->id,
            'notes'                 => $request['notes'],
            'created_by_id'         => Auth()->user()->id,
        ]);

        $this->sent_successfully();

        return back();
    }

    public function destroyNote($id)
    {
        $note = Note::findOrFail($id)->delete();

        $this->deleted();
        return back();
    }

    public function searchMember(Request $request)
    {
        $member = Lead::where('member_code', $request['search'])
            ->orWhere('name', $request['search'])
            ->orWhere('phone', $request['search'])
            ->orWhere('name', $request['search'])
            ->first();

        if ($member) {
            if ($member->type == 'member') {
                return redirect()->route('admin.members.show', $member->id);
            } else {
                return redirect()->route('admin.leads.show', $member->id);
            }
        } else {
            $this->no_data();
            return back();
        }
    }

    public function updateNote(Request $request, Note $note)
    {
        $note->update(['notes' => $request->edit_note]);
        $this->updated();
        return back();
    }
}
