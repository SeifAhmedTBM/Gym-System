<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\User;
use App\Models\Reminder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class RemindersImport implements ToCollection , WithHeadingRow, WithChunkReading, WithBatchInserts
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

    public function collection(Collection $collection)
    {
        // $sales = User::whereRelation('roles','title','Sales')->each(function($value,$key) use ($collection){
        //     $last_reminder = Reminder::get()->last();
        //     foreach ($collection as $row) 
        //     {
        //         if (!is_null($key+1) && $last_reminder->id !== $value->id) 
        //         {
        //             $member = Lead::wherePhone($row['phone'])->first();
    
        //             Reminder::take(5)->create([
        //                 'type'          => $row['type'],
        //                 'due_date'      => request('from'),
        //                 'lead_id'       => $member->id,
        //                 'user_id'       => $value->id,
        //             ]);
        //         }
        //     }
        // });
        
        $sales = User::whereRelation('roles','title','Sales')->get();
        
        foreach ($collection as $row) 
        {
            $member = Lead::wherePhone($row['phone'])->first();

            Reminder::create([
                'type'          => $row['type'],
                'due_date'      => request('from'),
                'lead_id'       => $member->id,
                // 'user_id'       => $sale->id,
            ]);
        }
    }
}