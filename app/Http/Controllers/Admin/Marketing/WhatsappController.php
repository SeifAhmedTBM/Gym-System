<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Models\Whatsapp;
use App\Models\Marketing;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\WhatsappMessageJob;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\WhatsappNumbersImport;
use App\Http\Requests\StoreWhatsappCampaigns;

class WhatsappController extends Controller
{
    public $token;

    public function __construct()
    {
        // $this->token = json_decode(Marketing::where('service', 'whatsapp')->first()->settings)->wassenger_token;        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $whatsapp_messages = Whatsapp::orderBy('id', 'desc')->get();
        return view('admin.marketing.whatsapp.index', compact('whatsapp_messages'));
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
    public function store(StoreWhatsappCampaigns $request)
    {
        if($file = $request->file('image')) {
            $name = time() . '_' . Str::random(15) .'.'.$file->getClientOriginalExtension();
            $file->move('images/marketing', $name);
            try {
                $response = Http::withToken($this->token)->post('https://api.wassenger.com/v1/files?reference='. Str::random(10) , [
                    'url' => asset('images/marketing/'.$name)
                ]);
                if(isset($response->json()['status']) && $response->json()['status'] == 409) {
                    $imageID = $response->json()['meta']['file'];
                }else {
                    $imageID = $response->json()[0]['id'];
                }
                
            } catch(\Exception $ex) {
                dd($ex->getMessage());
            }
        }

        if($csv = $request->file('numbers_csv')) {
            $csv_name = 'whats_numbers_csv.xlsx';
            $csv->move(public_path(), $csv_name);
            Excel::import(new WhatsappNumbersImport($imageID ?? NULL, $name ?? NULL, $request['message'], auth()->id()), $csv_name);
            $this->created();
        }else {
            try {
                $numbers = "";
                foreach(json_decode($request['numbers'], true) as $number) {
                    $numbers .= '+2' . $number['value'] . ",";
                }
                WhatsappMessageJob::dispatch(json_decode($request['numbers'], true), $imageID ?? NULL, $name ?? NULL, $request['message'], $numbers, auth()->id());
                $this->created();
            }catch(\Exception $ex) {
                dd($ex->getMessage());
            }
        }
        return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $whatsapp = Whatsapp::findOrFail($id);
        return view('admin.marketing.whatsapp.show', compact('whatsapp'));
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
        $whatsapp_message = Whatsapp::findOrFail($id);
        $whatsapp_message->delete();
        $this->deleted();
        return back();
    }
}
