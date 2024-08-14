<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Models\Sms;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\ImportSMSNumbers;
use App\Jobs\SendSMSJob;
use Maatwebsite\Excel\Facades\Excel;

class SmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sms = Sms::with('user')->paginate(10);
        return view('admin.marketing.sms.index', compact('sms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        if($file = $request->file('numbers_csv')) {
            $name = 'numbers.xlsx';
            $file->move(public_path(), $name);
            Excel::import(new ImportSMSNumbers($request['message']), $name);
            $this->created();
            return back();
        }

        // try {
        //     SendSMSJob::dispatch(json_decode($request['numbers'], true), $request['message'], auth()->id());
        //     $this->created();
        //     return back();
        // }catch(\Exception $ex) {
        //     dd($ex->getMessage());
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sms = Sms::findOrFail($id);
        return view('admin.marketing.sms.show', ['sms' => $sms]);
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
    public function destroy($id)
    {
        $sms = Sms::find($id);
        $sms->delete();
        $this->deleted();
        return back();
    }
}
