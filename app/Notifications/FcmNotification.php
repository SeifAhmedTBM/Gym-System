<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\FcmMessage;
use GuzzleHttp\Client;

class FcmNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $title;
    protected $body;
    protected $data;

    public function __construct($title, $body, $data = [])
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['fcm'];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setTitle($this->title)
            ->setBody($this->body)
            ->setData($this->data);
    }

    public function sendNotification($users)
    {
        $client = new Client();
        $url = 'https://fcm.googleapis.com/v1/projects/zfitness-6853f/messages:send';
        $accessToken = 'YOUR_ACCESS_TOKEN'; // Replace with your actual access token

        foreach ($users as $user) {
            if (!empty($user->fcm_token)) {
                $payload = [
                    'message' => [
                        'token' => $user->fcm_token,
                        'notification' => [
                            'title' => $this->title,
                            'body' => $this->body,
                        ],
                        'data' => $this->data,
                    ],
                ];

                $response = $client->post($url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $payload,
                ]);
            }
        }
    }
}