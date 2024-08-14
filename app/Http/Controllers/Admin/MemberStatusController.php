<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyMemberStatusRequest;
use App\Http\Requests\StoreMemberStatusRequest;
use App\Http\Requests\UpdateMemberStatusRequest;
use App\Models\MemberStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class MemberStatusController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('member_status_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = MemberStatus::query()->select(sprintf('%s.*', (new MemberStatus())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'member_status_show';
                $editGate = 'member_status_edit';
                $deleteGate = 'member_status_delete';
                $crudRoutePart = 'member-statuses';

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

            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });

            $table->editColumn('default_next_followup_days', function ($row) {
                return $row->default_next_followup_days ? $row->default_next_followup_days : '';
            });

            $table->editColumn('need_followup', function ($row) {
                return $row->need_followup ? MemberStatus::NEED_FOLLOWUP_SELECT[$row->need_followup] : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.memberStatuses.index');
    }

    public function create()
    {
        abort_if(Gate::denies('member_status_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.memberStatuses.create');
    }

    public function store(StoreMemberStatusRequest $request)
    {
        $memberStatus = MemberStatus::create($request->all());

        return redirect()->route('admin.member-statuses.index');
    }

    public function edit(MemberStatus $memberStatus)
    {
        abort_if(Gate::denies('member_status_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.memberStatuses.edit', compact('memberStatus'));
    }

    public function update(UpdateMemberStatusRequest $request, MemberStatus $memberStatus)
    {
        $memberStatus->update($request->all());

        return redirect()->route('admin.member-statuses.index');
    }

    public function show(MemberStatus $memberStatus)
    {
        abort_if(Gate::denies('member_status_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.memberStatuses.show', compact('memberStatus'));
    }

    public function destroy(MemberStatus $memberStatus)
    {
        abort_if(Gate::denies('member_status_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $memberStatus->delete();

        return back();
    }

    public function massDestroy(MassDestroyMemberStatusRequest $request)
    {
        MemberStatus::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getMemberStatus($id,$date)
    {
        $status = MemberStatus::findOrFail($id);
        $due_date = date('Y-m-d', strtotime($date . ' + ' . $status->default_next_followup_days . ' Days'));

        return response()->json([
            'status' => $status,
            'due_date' => $due_date
        ]);
    }
}
