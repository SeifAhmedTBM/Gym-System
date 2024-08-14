<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\TrainerService;
use App\Models\User;
use Illuminate\Http\Request;

class TrainerServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trainerServices = TrainerService::with(['trainer', 'service'])->simplePaginate(10);
        return view('admin.trainerServices.index', compact('trainerServices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = Service::pluck('name', 'id');
        $trainers = User::whereRelation('roles', 'title', 'Trainer')->pluck('name', 'id');
        return view('admin.trainerServices.create', compact('services', 'trainers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach($request->services as $key => $service_id) {
            TrainerService::create([
                'user_id'               => $request->user_id,
                'service_id'            => $service_id,
                'commission_type'       => $request['commission_type'][$key],
                'commission'            => $request['commission'][$key],
            ]);
        }
        $this->created();
        return redirect()->route('admin.trainer-services.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TrainerService $trainerService)
    {
        $trainerService->delete();
        $this->deleted();
        return back();
    }

    public function getServiceData(Request $request)
    {
        $services = [];
        foreach($request->services as $service) {
            array_push($services , [
                'name'      => Service::find($service)->name
            ]);
        }
        return response()->json($services);
    }
}
