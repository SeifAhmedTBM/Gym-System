<?php

namespace App\Imports\DataMigration;

use App\Models\Role;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToCollection , WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach($collection as $row) {
       

                    $phone = NULL;
                    if(Str::substr($row['phone'], 0, 1) == 0) {
                        $phone = $row['phone'];
                    }else {
                        $phone = "0" . $row['phone'];
                    }
                    
                if ($row['role'] !== 'office') {
                    $user = User::create([
                        'name'      => $row['name'],
                        'phone'     => $phone,
                        'email'     => $row['name'].$phone."@gmail.com",
                        'password'  => bcrypt('123456')
                    ]);
                    
                    if(isset($row['role']) && !is_null($row['role'])) {
                        $user->roles()->sync(Role::whereTitle(Str::ucfirst($row['role']))->first()->id);
                    }
                }

              

                Employee::create([
                    'job_status'            => 'fulltime',
                    'name'                  => $row['name'],
                    'start_date'            => date('Y-m-01'),
                    'finger_print_id'       => 0,
                    'target_amount'         => $row['target_amount'],
                    'salary'                => $row['salary'],
                    'status'                => 'active',
                    'branch_id'             => $row['branch_id'],
                    'user_id'               => $user->id ?? NULL,
                    'attendance_check'      => 'no',
                    'phone'                 => $phone
                ]);

        }
    }
}
