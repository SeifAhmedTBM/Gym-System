<?php
namespace App\Livewire;
use App\Models\Employee;
use Carbon\Carbon;
use Livewire\Component;

class CreateVacation extends Component
{
    public $vacations_balance;
    public $employee_id;
    public $from;
    public $to;
    public $diff = 0;

    public function render()
    {
        
        $employees = Employee::orderBy('name','asc')->where('status','active')->pluck('name','id')->prepend(trans('global.pleaseSelect'),'');

        if ($this->employee_id != null) 
        {
            $employee = Employee::withSum([
                'vacations' => fn($q) => $q->whereYear('created_at',date('Y'))
            ],'diff')->find($this->employee_id);
            // dd($employee);
            $this->vacations_balance = $employee->vacations_balance - $employee->vacations_sum_diff;
            
            if ($this->from != NULL && $this->to != NULL) 
            {
                $this->diff = Carbon::parse($this->from)->diffInDays(Carbon::parse($this->to));

                if ($this->vacations_balance < $this->diff) 
                {
                    $this->diff = 0;

                    $this->from = date('Y-m-d');
                    $this->to = date('Y-m-d');

                    $this->addError('diff','عدد الايام اكبر من الرصيد المتاح');
                    
                    // session()->flash('message', 'المبلغ المدفوع خاطئ !');
                }
            }
        }

        return view('livewire.create-vacation',compact('employees'));
    }
}
