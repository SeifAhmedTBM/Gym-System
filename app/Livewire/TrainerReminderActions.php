<?php

namespace App\Livewire;
use Livewire\Component;

class TrainerReminderActions extends Component
{
    public $action;
    public $due_date;
    public $due_date_active = false;

    public function updated()
    {
        if ($this->action != NULL) 
        {
            if ($this->action == 'appointment' || $this->action == 'follow_up' || $this->action == 'maybe') 
            {
                $this->due_date_active = true;
                $this->due_date = date('Y-m-d');
            }elseif ($this->action == 'no_answer') 
            {
                $this->due_date_active = true;
                $this->due_date = date('Y-m-d',strtotime('+1 Day'));
            }else{
                $this->due_date_active = false;
            }
        }
    }

    public function render()
    {
        return view('livewire.trainer-reminder-actions');
    }
}
