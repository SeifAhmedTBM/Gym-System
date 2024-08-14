<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\SalesTier;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreSalesTierRequest;
use App\Http\Requests\UpdateSalesTierRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroySalesTierRequest;
use App\Models\SalesTiersRange;
use App\Models\SalesTiersUser;

class SalesTiersController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('sales_tier_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = SalesTier::query()->select(sprintf('%s.*', (new SalesTier())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'sales_tier_show';
                $editGate = 'sales_tier_edit';
                // $deleteGate = 'sales_tier_delete';
                $crudRoutePart = 'sales-tiers';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    // 'deleteGate',
                    'editGate',
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

            $table->editColumn('month', function ($row) {
                return $row->month ? $row->month : '';
            });

            $table->editColumn('type', function ($row) {
                return $row->type ? SalesTier::TYPE_SELECT[$row->type] : '';
            });

            $table->editColumn('status', function ($row) {
                return $row->status ? SalesTier::STATUS_SELECT[$row->status] : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.salesTiers.index');
    }

    public function create()
    {
        abort_if(Gate::denies('sales_tier_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.salesTiers.create');
    }

    public function getUsersWithType($user_type)
    {
        $users = User::whereHas('roles', function($q) use($user_type) {
            $q = $q->where('title', Str::ucfirst($user_type));
        })->get();
        return response()->json(['users' => $users]);
    }

    public function store(StoreSalesTierRequest $request)
    {
        $salesTier = SalesTier::create([
            'month'     => $request['month'],
            'type'      => $request['type'],
            'status'    => $request['status'],
            'name'      => $request['name']
        ]);

        foreach($request['range_from'] as $key => $rangeFrom) {
            SalesTiersRange::create([
                'range_from' => $rangeFrom,
                'range_to'   => $request['range_to'][$key],
                'commission' => $request['commission'][$key],
                'sales_tier_id' => $salesTier->id
            ]);
        }

        foreach($request['users'] as $user) {
            SalesTiersUser::create(['user_id' => $user, 'sales_tier_id' => $salesTier->id]);
        }

        $this->created();
        
        return redirect()->route('admin.sales-tiers.index');
    }

    public function edit(SalesTier $salesTier)
    {
        abort_if(Gate::denies('sales_tier_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::whereHas('roles',function($q) use($salesTier)
        {
            $q->where('title',$salesTier->type);
        })->get();

        return view('admin.salesTiers.edit', compact('salesTier','users'));
    }

    public function update(UpdateSalesTierRequest $request, SalesTier $salesTier)
    {
        $salesTier->update([
            'month'     => $request['month'],
            'status'    => $request['status'],
            'name'      => $request['name']
        ]);

        foreach($request['range_from'] as $key => $rangeFrom) {
            SalesTiersRange::updateOrCreate([
                'sales_tier_id' => $salesTier->id,
                'id'            => $request['range'][$key] ?? '',
            ],
            [
                'range_from' => $rangeFrom,
                'range_to'   => $request['range_to'][$key],
                'commission' => $request['commission'][$key],
                'sales_tier_id' => $salesTier->id
            ]);
        }

        $salesTier->sales_tiers_users()->delete();
        
        foreach($request['users'] as $user) {
            SalesTiersUser::create([
                'user_id'       => $user, 
                'sales_tier_id' => $salesTier->id
            ]);
        }

        $this->updated();

        return redirect()->route('admin.sales-tiers.index');
    }

    public function show(SalesTier $salesTier)
    {
        abort_if(Gate::denies('sales_tier_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.salesTiers.show', compact('salesTier'));
    }

    public function destroy(SalesTier $salesTier)
    {
        abort_if(Gate::denies('sales_tier_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesTier->delete();

        return back();
    }

    public function massDestroy(MassDestroySalesTierRequest $request)
    {
        SalesTier::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function transferToNextMonth(Request $request, $id)
    {
        $sales = SalesTier::findOrFail($id);

        // Load old model relations
        $sales->load('sales_tiers_users', 'sales_tiers_ranges');

        //copy attributes
        $new = $sales->replicate();

        //save model before you recreate relations (so it has an id)
        $new->push();

        //re-sync everything
        foreach ($sales->getRelations() as $relation => $items){
            foreach($items as $item){
                unset($item->id);
                $new->{$relation}()->create($item->toArray());
            }
        }

        $new->month = date('Y-m', strtotime('+1 month', strtotime($sales->month)));
        $new->name = $request->name;
        $new->save();
        
        $this->updated();
        return back();
    }

    public function getSalesTierDetails($sales_tier_id)
    {
        $sales_tier = SalesTier::find($sales_tier_id);
        return response()->json($sales_tier);
    }
}
