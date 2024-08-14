<?php

namespace App\Http\Controllers\Admin;

use App\Models\Membership;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\RemindersImport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataMigration\LeadsImport;
use App\Imports\DataMigration\MembersImport;
use App\Imports\DataMigration\EmployeesImport;
use App\Imports\DataMigration\MasterDataImport;
use App\Imports\DataMigration\MembershipImport;

class MigrationController extends Controller
{
    public function migration()
    {
        return view('admin.migrations.index');
    }

    // Import Master Data TAB
    public function importMasterData(Request $request)
    {
        // try {
            $files = $request->except('_token');
            foreach($files as $file) {
                $fileName = Str::random(10) . '.'.$file->getClientOriginalExtension();
                $file->move('imports', $fileName);
                // Import Data
                Excel::import(new MasterDataImport ,'imports/' . $fileName);
            }
            $this->migrated();
            return back();
        // }catch(\Exception $ex) {
        //     session()->flash('xl_sheet_error', trans('global.xl_sheet_validation_error'));
        //     return back();
        // }
    }

    // Import Employees TAB
    public function importEmployees(Request $request)
    {
        try {
            $files = $request->except('_token');
            foreach($files as $file) 
            {
                $fileName = Str::random(10) . '.'.$file->getClientOriginalExtension();
                $file->move('imports', $fileName);
                // Import Data
                Excel::import(new EmployeesImport ,'imports/' . $fileName);
            }
            $this->migrated();
            return back();
        }catch(\Exception $ex) {
            dd($ex);
            session()->flash('xl_sheet_error', trans('global.xl_sheet_validation_error'));
            return back();
        }
    }

    // Import Leads TAB
    public function importLeadsAndMembers(Request $request)
    {
        try {
            if($leadsFile = $request->file('Lead')) {
                $leadsFileName =  Str::random(10) . '.' . $leadsFile->getClientOriginalExtension();
                $leadsFile->move('imports', $leadsFileName);
                // Import Data
                Excel::import(new LeadsImport , 'imports/' . $leadsFileName);
            }
            if($membersFile = $request->file('Member')) {
                $membersFileName = Str::random(10) . '.' . $membersFile->getClientOriginalExtension();
                $membersFile->move('imports', $membersFileName);
                // Import Data
                Excel::import(new MembersImport ,'imports/' . $membersFileName);
            }
            if($membershipFile = $request->file('Membership')) {
                $membershipFileName = Str::random(10) . '.' . $membershipFile->getClientOriginalExtension();
                $membershipFile->move('imports', $membershipFileName);
                // Import Data
                Excel::import(new MembershipImport ,'imports/' . $membershipFileName);
            }
            if($ReminderFile = $request->file('Reminder')) 
            {
                $ReminderFileName = Str::random(10) . '.' . $ReminderFile->getClientOriginalExtension();
                $ReminderFile->move('imports', $ReminderFileName);
                // Import Data
                Excel::import(new RemindersImport ,'imports/' . $ReminderFileName);
            }
            $this->migrated();
            return back();
        }catch(\Exception $ex) {
            dd($ex->getMessage());
            // session()->flash('xl_sheet_error', trans('global.xl_sheet_validation_error'));
            return back();
        }
    }

}
