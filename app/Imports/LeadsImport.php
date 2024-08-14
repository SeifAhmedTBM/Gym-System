<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\User;
use App\Models\Source;
use App\Models\Status;
use App\Models\Address;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;

class LeadsImport implements ToModel,WithStartRow,SkipsEmptyRows, WithValidation
{
    
    public function startRow() : int
    {
        return 2;
    } 

    public function rules(): array
    {
        return [
            '0' => 'required',
        ];
    }

    public function model(array $row)
    {
        $address = Address::whereName($row[2])->first();

        $source = Source::whereName($row[3])->first();

        $status = Status::whereName($row[5])->first();

        $user = User::whereHas('roles',function($q){
            $q->where('title','Sales');
        })->first();

        return new Lead([
            'name'          => $row[0],
            'phone'         => str_replace("'","",$row[1]),
            'address_id'    => $address->id ?? 1,
            'source_id'     => $source->id ?? 1,
            'gender'        => strtolower($row[4]) ?? 'male',
            'status_id'     => $status->id ?? 1,
            'type'          => 'lead',
            'sales_by_id'   => $user->id,
            'national'      => str_replace("'","",$row[7]),
            'notes'         => $row[8]
        ]);
    }
}
