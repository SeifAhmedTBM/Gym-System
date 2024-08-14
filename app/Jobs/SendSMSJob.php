<?php

namespace App\Jobs;

use App\Models\Marketing;
use App\Models\Sms;
// use Twilio\Rest\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $db_numbers;
    public $message;
    public $numbers;
    public $user_id;
    public $username;
    public $password;
    public $account_sid;
    public $data;
    public $ImportSMSNumbers;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db_numbers,$message,$user_id)
    {
        $data = json_decode(Marketing::where('service', 'sms')->first()->settings);
        $this->message = $message;
        $this->db_numbers = $db_numbers;
        $this->user_id = $user_id;
        $this->sender_id = $data;
        $this->username = $data->username;
        $this->password = $data->password;
        $this->account_sid = $data->account_sid;
    }

    // Start

    public function sendSms($mobile,$message,$sender_id){
        $curl = curl_init();
        $mobile = "2".$mobile;
       
        $message = rawurlencode($message);
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://smsmisr.com/api/SMS/?environment=1&username=c153511354627225c73f440a9f7831555a7522b7835f3771a938853f3928f611&password=1acb30ad4ebea5f760b4b214ba05d9fc73609cb5dbda773827d8b895d2d66f78&language=1&sender=21e19f507d24fae6263e120a5b05f44023a398020836985bf0ef830b164dbfda&mobile=".$mobile."&message=".$message,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => "",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
            "postman-token: 52686523-53eb-622a-86c8-cb66c246802c"
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }
        // dd(1);
    }

    public function start()
    {
        try {
            foreach($this->db_numbers as $number) 
            {
                $this->sendSms($number['value'],$this->message,$this->data);
                $this->numbers  = $this->numbers." , ".$number['value'];
            }

        }catch(\Exception $ex) {
            dd($ex->getMessage());
        }
    }


    public function end()
    {
        Sms::create([
            'sent_by' => $this->user_id,
            'message' => $this->message,
            'numbers' => rtrim($this->numbers, ",")
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->start();
            $this->end();
        }catch(\Exception $ex) {
            dd($ex->getMessage());
        }
    }
}
