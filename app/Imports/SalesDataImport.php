<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SalesDataImport implements ToCollection,WithChunkReading,WithBatchInserts, WithHeadingRow
{
    public function batchSize(): int
    {
        return 300;
    }
    /**
     * Chunk Size
     */
    public function chunkSize(): int
    {
        return 300;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) 
        {
            DB::transaction(function() use ($row)
            {
                $member = Lead::where('member_code',$row['member_code'])->first();
                if($member){
                    $member->update([
                        'sales_by_id'       => User::whereName($row['sales_by'])->first()->id
                    ]);
                }
                
            });
        }
    }
}
