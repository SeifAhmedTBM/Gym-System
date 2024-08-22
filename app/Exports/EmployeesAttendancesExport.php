<?php

namespace App\Exports;

use App\Models\EmployeeAttendance;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class EmployeesAttendancesExport implements FromCollection,WithHeadings
{
    use Exportable;

    public function collection()
    {
        $employee = Auth()->user()->employee;
        $from = request()->input('from') ?? null;
        $to = request()->input('to') ?? null;


        $branch_id = ($employee && $employee->branch_id != null)
            ? $employee->branch_id
            : (request()->input('branch_id') ?? '');

        $attendancesQuery = EmployeeAttendance::with('employee')
            ->whereHas(
                'employee', fn($q) => $q->whereHas(
                'user', fn($x) => $x
                ->when(request()->input('role_id'), fn($x) => $x->whereRelation('roles', 'id', request()->input('role_id')))
            )
                ->when($branch_id, fn($x) => $x->whereBranchId($branch_id))
                ->when(request()->input('employee_id'), fn($x) => $x->whereId(request()->input('employee_id')))
            );

        if ($from && $to) {
            $attendancesQuery->whereBetween('created_at', [$from, $to]);
        }
        elseif ($from) {
            // Only the from date is provided
            $attendancesQuery->where('created_at', '>=', $from);
        } elseif ($to) {
            // Only the to date is provided
            $attendancesQuery->where('created_at', '<=', $to);
        }else{
            $attendancesQuery->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'));
        }

        $attendances = $attendancesQuery->latest()->get();
                                        
        $attendances = $attendances->map(function ($attend) {
            return [
                'id'                     => $attend->id,
                'name'                   => $attend->employee->user ? $attend->employee->user->name : $attend->employee->name,
                'finger_print_id'        => $attend->employee->finger_print_id,
                'role'                   => $attend->employee->user->roles[0]->title ?? 'Employee',
                'clock_in'               => date("g:i A",strtotime($attend->clock_in)),
                'clock_out'              => !is_null($attend->clock_out) ? date("g:i:s A",strtotime($attend->clock_out)) : 'Still Working',
                'created_at'             => date('Y-m-d',strtotime($attend->created_at)),
            ];
        });

        return $attendances;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'FP ID',
            'Role',
            'In',
            'Out',
            'Created At',
        ];
    }
}
