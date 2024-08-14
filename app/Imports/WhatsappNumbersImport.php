<?php

namespace App\Imports;

use App\Jobs\WhatsappMessageJob;
use App\Models\Whatsapp;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class WhatsappNumbersImport
        implements 
        ToModel,
        WithStartRow,
        SkipsEmptyRows, 
        WithValidation, 
        WithEvents
{

    use RegistersEventListeners;

    public static $image;
    public static $numbers = [];
    public static $imageName;
    public static $message;
    public static $db_numbers;
    public static $user_id;

    public function __construct($image,$imageName, $message, $user_id)
    {
        self::$image = $image;
        self::$imageName = $imageName;
        self::$message = $message;
        self::$user_id = $user_id;
    }

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
        try {
            array_push(self::$numbers, [
                'value' => '0' . $row[0]
            ]);
            self::$db_numbers .= '0' . $row[0] . ",";
        }catch(\Exception $e) {
            dd($e->getMessage());
        }
    }

    /***
     * Import SMS
     */
    public static function afterImport(AfterImport $event)
    {
        try {
            WhatsappMessageJob::dispatch(self::$numbers, self::$image, self::$imageName, self::$message, self::$db_numbers, self::$user_id);
        }catch(\Exception $ex) {
            dd($ex->getMessage());
        }
    }
}
