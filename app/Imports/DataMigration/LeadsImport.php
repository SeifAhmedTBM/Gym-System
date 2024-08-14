<?php

namespace App\Imports\DataMigration;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\User;
use App\Models\Branch;
use App\Models\Source;
use App\Models\Status;
use App\Models\Address;
use App\Models\Reminder;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class LeadsImport implements ToCollection , WithHeadingRow, WithChunkReading, WithBatchInserts
{

    /**
     * Batch Size
     */
    public function batchSize(): int
    {
        return 1;
    }
    /**
     * Chunk Size
     */
    public function chunkSize(): int
    {
        return 1;
    }


    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        ini_set('max_execution_time', 3000);
        foreach ($collection as $index=> $row) 
        {
            // $sales_by   = User::whereName($row['sales_by'])->first();
            $phone      = '0'.$row['phone'];
            
            if($row['name'] != NULL) {
                $lead = Lead::firstOrCreate([
                    'phone'             => $phone,
                ],[
                    'name'              => $row['name'],
                    'type'              => 'lead',
                    'phone'             => $phone != NULL ? $phone : '01xxxxxxxxx',
                    // 'whatsapp_number'   => $row['whatsapp'] ?? NULL,
                    'gender'            => 'female',
                    // 'branch_id'         => Branch::first()->id ?? NULL,
                    'branch_id'         => 2,
                    'source_id'         => 11,
                    // 'source_id'         => Source::whereName($row['source'])->first()->id ?? Source::firstOrCreate(['name' => $row['source']])->id,
                    // 'branch_id'         => Branch::whereName($row['branch'])->first()->id ?? Branch::firstOrCreate(['name' => $row['branch']])->id,
                    // 'sales_by_id'       => $sales_by->id ?? User::first()->id,
                    'sales_by_id'       => 6415,
                    // 'created_by_id'     => $sales_by->id ?? User::first()->id,
                    'created_at'        => date('Y-m-d H:i:s'),
                ]);
               
                Reminder::create([
                    'type'              => 'sales',
                    'due_date'          => date('Y-m-d',strtotime('+1 day')),
                    // 'due_date'          => Date::excelToDateTimeObject($row['date'])->format('Y-m-d'),
                    'lead_id'           => $lead->id,
                    'user_id'           => 6415,
                ]);
              
            }
        }
    }
}
