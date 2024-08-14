<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class PayrollExport implements FromCollection,WithHeadings
{
    use Exportable;

    public function collection()
    {
        $date = request('date') != NULL ? request('date') : date('Y-m');

        $emp = Auth()->user()->employee;

        if ($emp && $emp->branch_id != NULL) 
        {
            $branch_id = $emp->branch_id;
        }else{
            $branch_id = request('branch_id') != NULL ? request('branch_id') : '';
        }

        $payrolls = Payroll::with([
                    'employee',
                    'employee.user',
                    'employee.branch'
                ])
                ->whereHas('employee', function($q) use ($branch_id)
                {
                    $q->whereHas('user',function($y){
                        $y->when(request('role_id'),fn($x) => $x->whereRelation('roles','id',request('role_id')));
                    })
                    ->when($branch_id,fn($x) => $x->whereBranchId($branch_id))
                    ->whereStatus('active');
                })
                ->whereYear('created_at',date('Y',strtotime($date)))
                ->whereMonth('created_at',date('m',strtotime($date)))
                ->get();

        $payrolls = $payrolls->map(function ($payroll) {
            return [
                'id'                    => $payroll->id,
                'name'                  => $payroll->employee->name ?? '-',
                'phone'                 => $payroll->employee->phone ?? '-',
                'role'                  => $payroll->employee->user->roles[0]->title ?? '-',
                'branch'                => $payroll->employee->branch->name ?? '-',
                'basic_salary'          => $payroll->basic_salary ?? '-',
                'bonus'                 => $payroll->bonus ?? '-',
                'deduction'             => $payroll->deduction ?? '-',
                'loans'                 => $payroll->loans ?? '-',
                'net_salary'            => $payroll->net_salary ?? '-',
                'status'                => $payroll->status ?? '-',
                'created_at'            => $payroll->created_at->format('Y-m') ?? '-',
            ];
        });

        return $payrolls;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Phone',
            'Role',
            'Branch',
            'Basic Salary',
            'Bouns',
            'Deduction',
            'Loans',
            'Net Salary',
            'Status',
            'Created At'
        ];
    }
}
