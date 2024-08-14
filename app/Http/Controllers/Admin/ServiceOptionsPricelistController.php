<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyServiceOptionsPricelistRequest;
use App\Http\Requests\StoreServiceOptionsPricelistRequest;
use App\Http\Requests\UpdateServiceOptionsPricelistRequest;
use App\Models\Pricelist;
use App\Models\ServiceOption;
use App\Models\ServiceOptionsPricelist;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceOptionsPricelistController extends Controller
{
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('service_options_pricelist_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $serviceOptionsPricelists = ServiceOptionsPricelist::with(['service_option', 'pricelist'])->get();

        return view('admin.serviceOptionsPricelists.index', compact('serviceOptionsPricelists'));
    }

    public function create()
    {
        abort_if(Gate::denies('service_options_pricelist_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $service_options = ServiceOption::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $pricelists = Pricelist::with('service')->get();

        return view('admin.serviceOptionsPricelists.create', compact('service_options', 'pricelists'));
    }

    public function store(StoreServiceOptionsPricelistRequest $request)
    {
        $serviceOptionsPricelist = ServiceOptionsPricelist::create($request->all());

        return redirect()->route('admin.service-options-pricelists.index');
    }

    public function edit(ServiceOptionsPricelist $serviceOptionsPricelist)
    {
        abort_if(Gate::denies('service_options_pricelist_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $service_options = ServiceOption::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $pricelists = Pricelist::with('service')->get();

        $serviceOptionsPricelist->load('service_option', 'pricelist');

        return view('admin.serviceOptionsPricelists.edit', compact('service_options', 'pricelists', 'serviceOptionsPricelist'));
    }

    public function update(UpdateServiceOptionsPricelistRequest $request, ServiceOptionsPricelist $serviceOptionsPricelist)
    {
        $serviceOptionsPricelist->update($request->all());

        return redirect()->route('admin.service-options-pricelists.index');
    }

    public function show(ServiceOptionsPricelist $serviceOptionsPricelist)
    {
        abort_if(Gate::denies('service_options_pricelist_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $serviceOptionsPricelist->load('service_option', 'pricelist');

        return view('admin.serviceOptionsPricelists.show', compact('serviceOptionsPricelist'));
    }

    public function destroy(ServiceOptionsPricelist $serviceOptionsPricelist)
    {
        abort_if(Gate::denies('service_options_pricelist_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $serviceOptionsPricelist->delete();

        return back();
    }

    public function massDestroy(MassDestroyServiceOptionsPricelistRequest $request)
    {
        ServiceOptionsPricelist::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
