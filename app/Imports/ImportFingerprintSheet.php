<?php

namespace App\Imports;

use App\Jobs\SaveFingerprintSheet;
use App\Models\EmployeeAttendance;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class ImportFingerprintSheet 
    implements 
    ToModel,
    WithStartRow,
    SkipsEmptyRows,
    WithValidation,
    WithEvents
{
    use RegistersEventListeners;
    use Importable;

    public static $finger_prints = [];
    public static $date = [];
    public static $clock_in = [];
    public static $clock_out = [];
    public static $work_time = [];
    public static $absent = [];
    public static $data = [];


    public function startRow() : int
    {
        return 2;
    } 


    public function rules(): array
    {
        return [
            '0' => 'required',
            '1' => 'required',
            '2' => 'nullable',
            '3' => 'nullable',
            '4' => 'nullable|in:True,False',
            '5' => 'nullable'
        ];
    }

 
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        array_push(self::$finger_prints , $row[0]);
        array_push(self::$date , $row[1]);
        array_push(self::$clock_in , $row[2]);
        array_push(self::$clock_out , $row[3]);
        array_push(self::$absent , $row[4]);
        array_push(self::$work_time , $row[5]);
    }


    /***
     * After Import FP Sheet
     */
    public static function afterImport(AfterImport $event)
    {
        self::$data = [
            'finger_print_id' => self::$finger_prints,
            'date' => self::$date,
            'clock_in' => self::$clock_in,
            'clock_out' => self::$clock_out,
            'absent' => self::$absent,
            'work_time' => self::$work_time
        ];
        SaveFingerprintSheet::dispatch(self::$data);
    }
}
