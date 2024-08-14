<?php

namespace App\Imports;

use App\Models\MailCamps;
use App\Mail\MailCampaign;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class SendEmailImport 
    implements 
    ToModel,
    WithStartRow,
    SkipsEmptyRows, 
    WithValidation, 
    WithEvents
{
    use RegistersEventListeners;
    use Importable;

    public static $emails;
    public $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function startRow() : int
    {
        return 2;
    }

    public function rules(): array
    {
        return [
            '0' => 'required|email'
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        Mail::to($row[0])->send(new MailCampaign($this->data));
        self::$emails .= $row[0] . ",";
    }


    /***
     * After Import SMS
     */
    public static function afterImport(AfterImport $event)
    {
        return MailCamps::create([
            'emails'    => rtrim(self::$emails, ","),
            'message'   => request()->message,
            'sent_by'   => auth()->id(),
            'subject'   => request()->subject
        ]);
    }
}
