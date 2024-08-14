<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lead;
use App\Models\User;
use App\Models\Status;
use App\Models\Reminder;
use App\Models\Membership;
use Illuminate\Support\Str;
use App\Models\MemberStatus;
use Illuminate\Http\Request;
use App\Models\MemberReminder;
use App\Exports\RemindersExport;
use App\Imports\RemindersImport;
use App\Http\Controllers\Controller;
use App\Models\LeadRemindersHistory;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MemberRemindersHistory;
use App\Exports\OverdueRemindersExport;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\UpcommingRemindersExport;
use App\Http\Requests\StoreReminderRequest;
use App\Http\Requests\UpdateReminderRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyReminderRequest;

class RemindersController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
     
        abort_if(Gate::denies('reminder_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $date = isset($request->date) ? $request->date : date('Y-m-d');

        if (auth()->user()->roles[0]->title == 'Sales') {
            $reminders = Reminder::index($request->all())
                ->whereHas('lead')
                ->whereUserId(Auth()->user()->id)
                ->whereDate('due_date', $date)
                ->latest()
                ->get();
        } else if (auth()->user()->roles[0]->title == 'Trainer') {
            $reminders = Reminder::index($request->all())
                ->whereUserId(Auth()->user()->id)
                ->whereDate('due_date', $date)
                ->orderBy('due_date', 'desc')
                ->get();
        } elseif (auth()->user()->roles[0]->title = 'Fitness Manager') {
            $reminders = Reminder::index($request->all())
                ->whereUserId($request['user_id'])
                ->whereDate('due_date', $date)
                ->orderBy('due_date', 'desc')
                ->get();
        } else {
            $reminders = Reminder::index($request->all())
                ->whereHas('lead')
                ->whereDate('due_date', $date)
                ->orderBy('due_date', 'desc')
                ->get();
        }

        $sales = User::whereRelation('roles', 'title', 'sales')->pluck('name', 'id');

        return view('admin.reminders.index', compact('reminders', 'sales'));
    }

    public function create()
    {
        abort_if(Gate::denies('reminder_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $leads = Lead::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.reminders.create', compact('leads', 'users'));
    }

    public function store(StoreReminderRequest $request)
    {
        $reminder = Reminder::create($request->all());

        return redirect()->route('admin.reminders.index');
    }

    public function edit(Reminder $reminder)
    {
        abort_if(Gate::denies('reminder_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $leads = Lead::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $reminder->load('lead', 'user', 'member_status');

        return view('admin.reminders.edit', compact('leads', 'users', 'reminder'));
    }

    public function update(UpdateReminderRequest $request, Reminder $reminder)
    {
        $reminder->update($request->all());

        return redirect()->route('admin.reminders.index');
    }

    public function show(Reminder $reminder)
    {
        abort_if(Gate::denies('reminder_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $reminder->load('lead', 'user');

        return view('admin.reminders.show', compact('reminder'));
    }

    public function destroy(Reminder $reminder)
    {
        abort_if(Gate::denies('reminder_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $reminder->delete();

        return back();
    }

    public function massDestroy(MassDestroyReminderRequest $request)
    {
        Reminder::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function upcomming(Request $request)
    {
        abort_if(Gate::denies('reminder_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $date = isset($request->date) ? $request->date : date('Y-m-d');

        if (auth()->user()->roles[0]->title == 'Sales') {
            $reminders = Reminder::index($request->all())
                ->whereHas('lead')
                ->whereUserId(Auth()->user()->id)
                ->whereDate('due_date', '>', $date)
                ->latest()
                ->get();

        } else if (auth()->user()->roles[0]->title == 'Trainer') {
            $reminders = Reminder::index($request->all())
                ->whereUserId(Auth()->user()->id)
                ->whereDate('due_date','>',$date)
                ->orderBy('due_date', 'desc')
                ->get();

        } elseif (auth()->user()->roles[0]->title = 'Fitness Manager') {
            $reminders = Reminder::index($request->all())
                ->whereUserId($request['user_id'])
                ->whereDate('due_date','>',$date)
                ->orderBy('due_date', 'desc')
                ->get();

        } else {
            $reminders = Reminder::index($request->all())
                ->whereHas('lead')
                ->whereDate('due_date', '>', $date)
                ->orderBy('due_date', 'desc')
                ->get();
        }

        $sales = User::whereRelation('roles', 'title', 'sales')->pluck('name', 'id');

        return view('admin.reminders.upcomming', compact('reminders', 'sales'));
    }

    public function overdue(Request $request)
    {
        abort_if(Gate::denies('reminder_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $date = isset($request->date) ? $request->date : date('Y-m-d');

        if (auth()->user()->roles[0]->title == 'Sales') {

            $reminders = Reminder::index($request->all())
                ->whereHas('lead')
                ->whereUserId(Auth()->user()->id)
                ->whereDate('due_date', '<', $date)
                ->latest()
                ->get();
        } else if (auth()->user()->roles[0]->title == 'Trainer') {
            $reminders = Reminder::index($request->all())
                ->whereUserId(Auth()->user()->id)
                ->whereDate('due_date', '<', $date)
                ->orderBy('due_date', 'desc')
                ->get();
        } elseif (auth()->user()->roles[0]->title = 'Fitness Manager') {
            $reminders = Reminder::index($request->all())
                ->whereUserId($request['user_id'])
                ->whereDate('due_date', '<', $date)
                ->orderBy('due_date', 'desc')
                ->get();
        } else {
            $reminders = Reminder::index($request->all())
                ->whereHas('lead')
                ->whereDate('due_date', '<', $date)
                ->orderBy('due_date', 'desc')
                ->get();
        }

        $sales = User::whereRelation('roles', 'title', 'sales')->pluck('name', 'id');

        return view('admin.reminders.overdue', compact('reminders', 'sales'));
    }

    public function takeLeadAction(Request $request, $id)
    {
        // dd($request->all());
        $reminder = Reminder::findOrFail($id);

        $request->validate([
            // 'status_id'     => 'bail|required|integer|exists:statuses,id'
        ]);

        // Lead::find($reminder->lead_id)->update(['status_id' => $request['status_id']]);

        if (!is_null($request['due_date'])) {
            $newReminder = Reminder::create([
                'type'          => $reminder->type,
                'membership_id' => $reminder->membership_id,
                'lead_id'       => $reminder->lead_id,
                'due_date'      => $request['due_date'],
                'action'        => $request['action'],
                'user_id'       => $reminder->user_id,
                'notes'             => $request['notes'],
            ]);
        }
        if (($request['action'] == 'not_interested')) 
        {
            LeadRemindersHistory::create([
                'type'          => $reminder->type,
                'membership_id' => $reminder->membership_id,
                'lead_id'       => $reminder->lead_id,
                'due_date'      => $reminder->due_date,
                'action_date'   => date('Y-m-d'),
                'action'        => 'not_interested',
                'notes'         => $request['notes'],
                'user_id'       => $reminder->user_id,

            ]);
        } else {
            LeadRemindersHistory::create([
                'type'          => $reminder->type,
                'membership_id' => $reminder->membership_id,
                'lead_id'       => $reminder->lead_id,
                'due_date'      => $reminder->due_date,
                'action_date'   => date('Y-m-d'),
                'action'        => $request['action'],
                'notes'         => $request['notes'],
                'user_id'       => $reminder->user_id,
            ]);
        }


        // if ($request['action'] = 'not_interested') 
        // {
        //     LeadRemindersHistory::create([
        //         'type'          => $reminder->type,
        //         'membership_id' => $reminder->membership_id,
        //         'lead_id'       => $reminder->lead_id,
        //         'due_date'      => $reminder->due_date,
        //         'action_date'   => date('Y-m-d'),
        //         'action'        => 'not_interested',
        //         'notes'         => NULL,
        //         'user_id'       => $reminder->user_id,
        //         // 'created_at'    => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . "+1 minutes"))
        //     ]);
        // }

        $reminder->delete();

        $this->created();
        return back();
    }

    public function takeMemberAction(Request $request, $id)
    {
        $request->validate([
            // 'status_id'     => 'bail|required|integer|exists:statuses,id'
        ]);

        $member = Lead::with('leadReminder')->whereType('member')->findOrFail($id);

        $sales_by_id = $member->sales_by_id;

        $member->update(['status_id' => $request['status_id']]);


        // if ($member->leadReminder) {
        //     $member->leadReminder()->delete();
        // }

        if (!is_null($request['due_date'])) {
            $newReminder = Reminder::create([
                'type'              => 'custom',
                'lead_id'           => $member->id,
                'due_date'          => $request['due_date'],
                'action'            => $request['action'],
                'user_id'           => $sales_by_id,
                'notes'             => $request['notes'],
            ]);
        }


        // LeadRemindersHistory::create([
        //     'lead_id'       => $member->id,
        //     'type'          => 'custom',
        //     'due_date'      => $request['due_date'],
        //     'action_date'   => date('Y-m-d'),
        //     'notes'         => $request['notes'],
        //     'user_id'       => $sales_by_id,
        // ]);

        $this->created();
        return back();
    }

    public function leadAction(Request $request, $id)
    {
        // return $request->all();
        $request->validate([
            // 'status_id'     => 'bail|required|integer|exists:statuses,id'
        ]);

        $lead = Lead::with('leadReminder')->findOrFail($id);

        $sales_by_id = $lead->sales_by_id;

        $lead->update(['status_id' => $request['status_id']]);

        // if ($lead->leadReminder) {
        //     $lead->leadReminder()->delete();
        // }

        if (!is_null($request['due_date'])) {
            $newReminder = Reminder::create([
                'type'          => 'custom',
                'lead_id'       => $lead->id,
                'due_date'      => $request['due_date'],
                'action'        => $request['action'],
                'user_id'       => $sales_by_id,
                'notes'             => $request['notes'],
            ]);
        }

        // LeadRemindersHistory::create([
        //     'lead_id'       => $lead->id,
        //     'type'          => 'custom',
        //     'due_date'      => $request['due_date'],
        //     'action_date'   => date('Y-m-d'),
        //     'notes'         => $request['notes'],
        //     'user_id'       => $sales_by_id,
        // ]);

        $this->created();
        return back();
    }

    public function remindersManagement()
    {
        return view('admin.reminders.reminder_management');
    }

    public function importReminders(Request $request)
    {
        // return $request->all();
        // try {

        //     if($RemindersFile = $request->file('upload_reminder')) {
        //         $RemindersFileName =  Str::random(10) . '.' . $RemindersFile->getClientOriginalExtension();
        //         $RemindersFile->move('imports', $RemindersFileName);
        //         // Import Data
        //         Excel::import(new RemindersImport , 'imports/' . $RemindersFileName);
        //     }
        // }catch(\Exception $ex) {
        //     dd($ex->getMessage());
        //     return back();
        // }
        // // Excel::import(new RemindersImport,$request->upload_reminder);
        // $this->created();

        // return back();

    }

    public function remindersHistory(Request $request)
    {
        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language', '_']);

        $sale = Auth()->user();

        if ($sale->roles[0]->title == 'Sales') {
            $histories = LeadRemindersHistory::index($data)->whereUserId($sale->id)->latest()->get();
        } else {
            $histories = LeadRemindersHistory::index($data)->latest()->get();
        }


        return view('admin.reminders.reminderHistory', compact('histories'));
    }

    public function destroyReminderhistory($id)
    {
        $reminderHistory = LeadRemindersHistory::findOrFail($id)->delete();

        $this->deleted();
        return back();
    }

    public function export(Request $request)
    {
        return Excel::download(new RemindersExport($request), 'reminders.xlsx');
    }

    public function exportUpcomming(Request $request)
    {
        return Excel::download(new UpcommingRemindersExport($request), 'upcomming-reminders.xlsx');
    }

    public function exportOverdue(Request $request)
    {
        return Excel::download(new OverdueRemindersExport($request), 'overdue-reminders.xlsx');
    }

    public function assign_reminder(Request $request, $id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->update([
            'user_id'       => $request['user_id']
        ]);

        $this->sent_successfully();
        return back();
    }

    public function take_trainer_reminder(Request $request,Lead $member)
    {
        $last_membership = Membership::whereMemberId($member->id)
                                    ->whereIn('status',['current','expired','expiring'])
                                    ->latest()
                                    ->first();

        // dd($request->all(),$last_membership);
        
        if ($last_membership) 
        {
            if (!is_null($request['due_date'])) 
            {
                $reminder = Reminder::create([
                    'type'          => 'pt_session',
                    'lead_id'       => $member->id,
                    'due_date'      => $request['due_date'],
                    'action'        => $request['action'],
                    'user_id'       => $request['trainer_id'],
                    'notes'         => $request['notes'],
                ]);
            }

            $this->sent_successfully();
        }else{
            $this->something_wrong();
        }
        
        return back();
    }
}
