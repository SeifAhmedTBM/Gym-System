<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpensesExport implements FromCollection,WithHeadings
{
    use Exportable;

    public function collection()
    {
        $expenses = Expense::index(request()->all())
                            ->with(['expenses_category','account','created_by'])
                            ->latest()
                            ->get();

        $expenses = $expenses->map(function ($expense) {
            return [
                'id'                     => $expense->id,
                'name'                   => $expense->name,
                'date'                   => $expense->date,
                'account'                => $expense->account->name ?? '-',
                'amount'                 => $expense->amount ?? '-',
                'expenses_category_id'   => $expense->expenses_category->name ?? '-',
                'created_by'             => $expense->created_by->name ?? '-',
                'created_at'             => $expense->created_at->format('Y-m-d'),
                
            ];
        });

        return $expenses;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Date',
            'Account',
            'Amount',
            'Expenses Category',
            'Created By',
            'Created At'
        ];
    }
}
