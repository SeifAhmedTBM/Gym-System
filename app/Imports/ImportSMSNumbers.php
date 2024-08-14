<?php

namespace App\Imports;

use App\Models\Sms;
use Twilio\Rest\Client;
use App\Jobs\SendSMSJob;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class ImportSMSNumbers 
    implements 
    ToModel,
    WithStartRow,
    SkipsEmptyRows, 
    WithValidation, 
    WithEvents
{
    use RegistersEventListeners;
    use Importable;

    public static $numbers = [];

    public function startRow() : int
    {
        return 2;
    }

    public function rules(): array
    {
        return [
            '0' => 'required|numeric'
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        array_push(self::$numbers , [
            'value' => '0' . $row[0]
        ]);
    }

    /***
     * After Import SMS
     */
    public static function afterImport(AfterImport $event)
    {
        SendSMSJob::dispatch(self::$numbers, request()->message, auth()->id());
    }
}
