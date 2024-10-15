<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Lead;
use App\Models\User;
use App\Models\notifications;
use App\Models\user_notifications;
use App\Notifications\FcmNotification;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
class NotificationController extends Controller
{
    public function index(){
        $branches = Branch::get();
        $notifications = notifications::get();

        return view('admin.Notifications.index' , compact('notifications'));
    }
    public function create(){
        $branches = Branch::get();
      

        return view('admin.Notifications.create' , compact('branches'));
    }


    public function sendNotification(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'body' => 'required|string',
            'branch' => 'nullable|integer',
        ]);
    
        $title = $request->title;
        $body = $request->body;
    
        $notification = new notifications();
        $notification->title = $title;
        $notification->body = $body;
        $notification->save();
        
        
        if ($request->branch_id == 0) {
            $fcm_tokens = Lead::whereNotNull('fcm_token')->select('fcm_token','user_id')->get();
        } else {
            $fcm_tokens = Lead::where([
                ['branch_id', $request->branch_id] ,
                ['fcm_token' , '<>', null],
                ])->select('fcm_token','user_id')->get();
        }

        foreach($fcm_tokens as $fcm_token){
            $user = new user_notifications();
            $user->user_id = $fcm_token->user_id;
            $user->notification_id = $notification->id;
            $user->save();
        }
   
        $notificationStatus = [];
 
        $projectId = config('services.firebase.project_id'); 

        $credentialsFilePath = Storage::path('json/zfitness-cfd0a-firebase-adminsdk-4rccc-4936322033.json');
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

        foreach ($fcm_tokens as $fcm_token) {
           
            $data = [
                "message" => [
                    "token" => $fcm_token->fcm_token,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                    ],
                ]
            ];
    
            $payload = json_encode($data);
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_VERBOSE, true); 
    
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
    
            if ($err) {
                return response()->json([
                    'message' => 'Curl Error: ' . $err
                ], 500);
            } 
            else 
            {
          
                $notificationStatus[] = json_decode($response, true);
            }
            
        }
       
        // return response()->json([
        //     'message' => 'Notifications have been sent',
        //     'responses' => $notificationStatus
        // ]);
        session()->flash('success','Notification Sent Succefully');
        return back();
    }

    
}
