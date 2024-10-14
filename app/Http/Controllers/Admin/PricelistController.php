<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Status;
use App\Models\Service;
use App\Models\Pricelist;
use App\Models\MobileSetting;
use Illuminate\Http\Request;
use App\Models\PricelistDays;
use App\Models\ServiceOption;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Models\ServiceOptionsPricelist;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StorePricelistRequest;
use App\Http\Requests\UpdatePricelistRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyPricelistRequest;

class PricelistController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('pricelist_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {       
            $query = Pricelist::with(['service','pricelist_days'])->select(sprintf('%s.*', (new Pricelist())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'pricelist_show';
                $editGate = 'pricelist_edit';
                $deleteGate = 'pricelist_delete';
                $crudRoutePart = 'pricelists';

                return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
            });

            $table->editColumn('id', function ($row) {
                return $row->order ? $row->order : '';
            });
            $table->editColumn('amount', function ($row) {
                return $row->amount ? number_format($row->amount) : '';
            });
            $table->editColumn('freeze_count', function ($row) {
                return $row->freeze_count ? $row->freeze_count : '-';
            });
            $table->editColumn('session_count', function ($row) {
                return $row->session_count ? $row->session_count : '-';
            });
         
            $table->editColumn('upgrade_from', function ($row) {
                return $row->upgrade_from ? $row->upgrade_from : '-';
            });
            $table->editColumn('upgrade_to', function ($row) {
                return $row->upgrade_to ? $row->upgrade_to : '-';
            });

            $table->editColumn('expiring_date', function ($row) {
                return $row->service->service_type->session_type == 'non_sessions' ? $row->expiring_date ?? 0 : '<span class="badge badge-danger">Not Available</span>';
            });

            $table->editColumn('expiring_session', function ($row) {
                return $row->service->service_type->session_type == 'sessions' ? $row->expiring_session ?? 0 : '<span class="badge badge-danger">Not Available</span>';
            });
            
            $table->addColumn('service_name', function ($row) {
                return $row->service ? $row->service->name : '-';
            });

            $table->addColumn('status', function ($row) {
                return $row->status ? $row->status : '';
            });

            $table->editColumn('full_day', function ($row) {
                return $row->full_day == 'true' ? "<span class='badge badge-success'>Yes</span>" : "<span class='badge badge-danger'>No</span>";
            });

            $table->addColumn('all_days', function ($row) {
                return $row->pricelist_days->count() > 0 ? "<span class='badge badge-danger'>No</span>" : "<span class='badge badge-success'>Yes</span>";
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->editColumn('main_service', function ($row) {
                return \App\Models\Pricelist::MAIN_SERVICE[$row->main_service];
            });

            $table->rawColumns(['actions', 'placeholder', 'service', 'status','expiring_date','expiring_session','full_day','all_days']);

            return $table->make(true);
        }

        return view('admin.pricelists.index');
    }

    public function create($id)
    {
        
        abort_if(Gate::denies('pricelist_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $service = Service::findOrFail($id);

        $services = Service::whereStatus('active')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        
        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $class_service_id = MobileSetting::all()->first()->classes_service_type;
        $serviceOptions = ServiceOption::get();

        return view('admin.pricelists.create', compact('services','serviceOptions','branches','service','class_service_id'));
    }

    public function store(StorePricelistRequest $request)
    {
        $pricelist = Pricelist::create([
            'name'              => $request['name'],
            'amount'            => $request['amount'],
            'order'             => $request['order'],
            'invitation'        => $request['invitation'],
            'service_id'        => $request['service_id'],
            'status'            => $request['status'],
            'upgrade_from'      => $request['upgrade_from'],
            'upgrade_to'        => $request['upgrade_to'],
            'expiring_date'     => $request['expiring_date'],
            'expiring_session'  => $request['expiring_session'],
            'session_count'     => $request['session_count'],
            'freeze_count'      => $request['freeze_count'],
            'max_count'      => $request['max_count'] ?? null,
            'followup_date'     => $request['followup_date'],
            'main_service'      => isset($request['main_service']) ? true : false,
            'full_day'          => isset($request['full_day']) == 'true' ? 'true' : 'false',
            'from'              => isset($request['full_day']) ? NULL : $request['from'],
            'to'                => isset($request['full_day']) ? NULL : $request['to'],
            'all_branches'      => isset($request['all_branches']) == 'true' ? 'true' : 'false',
        ]);

        if (!isset($request['all_days']))
        {
            foreach ($request['allDays'] as $key => $day) 
            {
                $pricelist_days = PricelistDays::create([
                    'pricelist_id'  => $pricelist->id,
                    'day'           => $day
                ]);
            }
        }

        if (isset($request['count'])) {
            foreach ($request['count'] as $key => $service) {
                $serviceOptionsPricelist = new ServiceOptionsPricelist;
                $serviceOptionsPricelist->pricelist_id = $pricelist->id;
                $serviceOptionsPricelist->service_option_id = $request->service_ids[$key];
                $serviceOptionsPricelist->count = $service;
                $serviceOptionsPricelist->save();
            }
        }

        $this->sent_successfully();
        return redirect()->route('admin.pricelists.index');
    }

    public function edit(Pricelist $pricelist)
    {
        abort_if(Gate::denies('pricelist_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $services = Service::whereStatus('active')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        
        $serviceOptions = ServiceOption::get();
        
        $pricelist->load(['service','serviceOptionsPricelist','pricelist_days'])->loadCount('pricelist_days');
        $class_service_id = MobileSetting::all()->first()->classes_service_type;

        $branches = Branch::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.pricelists.edit', compact('services', 'pricelist','serviceOptions','branches','class_service_id'));
    }

    public function update(UpdatePricelistRequest $request, Pricelist $pricelist)
    {   
        // return $request->all();
        $pricelist->update([
            'name'              => $request['name'],
            'amount'            => $request['amount'],
            'order'            => $request['order'],
            'invitation'        => $request['invitation'],
            'service_id'        => $request['service_id'],
            'status'            => $request['status'],
            'upgrade_from'      => $request['upgrade_from'],
            'upgrade_to'        => $request['upgrade_to'],
            'expiring_date'     => $request['expiring_date'],
            'expiring_session'  => $request['expiring_session'],
            'session_count'     => $request['session_count'],
            'max_count'      => $request['max_count'] ?? null,
            'freeze_count'      => $request['freeze_count'],
            'followup_date'     => $request['followup_date'],
            'main_service'      => isset($request['main_service']) ? true : false,
            'full_day'          => isset($request['full_day']) == 'true' ? 'true' : 'false',
            'from'              => isset($request['full_day']) ? NULL : $request['from'],
            'to'                => isset($request['full_day']) ? NULL : $request['to'],
            'all_branches'      => isset($request['all_branches']) == 'true' ? 'true' : 'false',
        ]);

        if (!isset($request['all_days']))
        {
            foreach ($request['allDays'] as $key => $day) 
            {
                $pricelist->pricelist_days()->updateOrCreate([
                    'day'           => $day
                ]);
            }
        }else{
            $pricelist->pricelist_days()->delete();
        }
        
        if (isset($request['count'])) {
            foreach ($request['count'] as $key => $service) {
                $pricelist->serviceOptionsPricelist()->updateOrCreate(
                    ['service_option_id' => $request['service_ids'][$key]],
                    ['count' => $service]);
            }
        }

        $this->updated();
        return redirect()->route('admin.pricelists.index');
    }

    public function show(Pricelist $pricelist)
    {
        abort_if(Gate::denies('pricelist_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pricelist->load('service','pricelist_days')->loadCount('pricelist_days');

        return view('admin.pricelists.show', compact('pricelist'));
    }

    public function destroy(Pricelist $pricelist)
    {
        abort_if(Gate::denies('pricelist_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pricelist->delete();

        return back();
    }

    public function massDestroy(MassDestroyPricelistRequest $request)
    {
        Pricelist::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getServicesByPricelist($id, $date)
    {
        $pricelist = Pricelist::with('service')->find($id);

        $date = Carbon::parse($date);
        $expiry_date = $pricelist->service->type == 'days' ? $date->addDays($pricelist->service->expiry)->format('Y-m-d') : $date->addMonths($pricelist->service->expiry)->format('Y-m-d');
        
        return response()->json(['expiry' => $expiry_date, 'pricelist' => $pricelist]);
    }

    public function servicePricelists($id)
    {
        $service = Service::findOrFail($id);
        $service->load(['service_pricelist']);

        return view('admin.services.pricelists',compact('service'));
    }
}
