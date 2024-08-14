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
        $attendances = EmployeeAttendance::index(request()->all())
                                            ->with(['employee' => fn($q) => $q->with('user')])
                                            ->whereHas('employee')
                                            ->latest()
                                            ->get();
                                        
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
