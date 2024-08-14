<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Models\MailCamps;
use App\Mail\MailCampaign;
use Illuminate\Http\Request;
use App\Imports\SendEmailImport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\SendEmailCampaignRequest;

class MailCampsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mailCamps = MailCamps::simplePaginate(10);

        return view('admin.marketing.mail.index', compact('mailCamps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.marketing.mail.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SendEmailCampaignRequest $request)
    {
        // Data that will be sent through the email
        $data = [
            'body' => $request['message'],
            'subject' => $request['subject']
        ];

        if($file = $request->file('emails_csv')) {
            $name = 'emails_csv.xlsx';
            $file->move(public_path(), $name);
            Excel::import(new SendEmailImport($data), $name);
            $this->created();
            return redirect()->route('admin.marketing.mails.index');
        }

        // Email with custom syntax to save into database
        $emails = "";

       // Loop through the coming emails and create job for each one 
        foreach(json_decode($request['emails'], true) as $email) {

            try {

                Mail::to($email['value'])->send(new MailCampaign($data));

            } catch (\Exception $ex) {

                dd($ex->getMessage());

            }

            $emails .= $email['value'] . ",";
        }
        // Create database record for the sent emails
        MailCamps::create([
            'emails'    => rtrim($emails , ","),
            'message'   => $request['message'],
            'subject'   => $request['subject'],
            'sent_by'   => auth()->id()
        ]);
        // Show created successfully message
        $this->created();
        

        // Call Artisan Command On The Server
        // Artisan::call('queue:work --stop-when-empty');


        // Redirect back to the index route of mails
        return redirect()->route('admin.marketing.mails.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mailCamp = MailCamps::findOrFail($id);
        return view('admin.marketing.mail.view_campaigns', compact('mailCamp'));
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
        $mailCamp = MailCamps::findOrFail($id);
        $mailCamp->delete();
        $this->deleted();
        return back();
    }
}
