<?php

namespace App\Jobs;

use App\Models\Whatsapp;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class WhatsappMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $numbers;
    public $imageID;
    public $imageName;
    public $message;
    public $db_numbers;
    public $user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($numbers, $imageID, $imageName, $message, $db_numbers, $user_id)
    {
        $this->numbers = $numbers;
        $this->imageID = $imageID;
        $this->imageName = $imageName;
        $this->message = $message;
        $this->db_numbers = $db_numbers;
        $this->user_id = $user_id;
    }

    /**
     * Start Method
     */
    public function start()
    {
        try {
            foreach($this->numbers as $number) {
                if($this->imageID != NULL) {
                    Http::withToken(config('marketing.w_a_token'))->post('https://api.wassenger.com/v1/messages', [
                        'phone'     => '+2' . $number['value'],
                        'message'   => $this->message,
                        'media'     => ['file' => $this->imageID]
                    ]);
                }else {
                    Http::withToken(config('marketing.w_a_token'))->post('https://api.wassenger.com/v1/messages', [
                        'phone'     => '+2' . $number['value'],
                        'message'   => $this->message
                    ]);
                }
            }
        }catch(\Exception $ex) {
            dd($ex->getMessage());
        }
    }

    /**
     * End Method
     */
    public function end()
    {
        try {
            Whatsapp::create([
                'numbers'   => rtrim($this->db_numbers, ","),
                'message'   => $this->message,
                'sent_by'   => $this->user_id,
                'image_id'  => $this->imageName == NULL ? NULL : asset('images/marketing/' . $this->imageName)
            ]);
        }catch(\Exception $ex) {
            dd($ex->getMessage());
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->start();
        $this->end();
    }
}
