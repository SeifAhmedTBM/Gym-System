<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\FcmNotification;

class NotificationController extends Controller
{
    public function index(){
        $branches = Branch::get();
        return view('admin.Notifications.index' , compact('branches'));
    }


    public function sendNotification(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'body' => 'required|string',
            'branch' => 'nullable|integer',
        ]);
    
        $title = 'Hello M.F';
        $body = $request->body;
    
        // Retrieve users based on branch and fcm_token
        $users = $request->branch 
            ? Lead::where('branch_id', $request->branch)->whereNotNull('fcm_token')->get() 
            : Lead::whereNotNull('branch_id')->whereNotNull('fcm_token')->get();

        $notificationStatus = [];
        // Send notifications
        foreach ($users as $user) {
            try {
               
                $user->notify(new FcmNotification($title, $body));
                $notificationStatus[] = [
                    'user_id' => $user->id,
                    'status' => 'success',
                    'message' => 'Notification sent successfully',
                ];
            } catch (\Exception $e) { 
                $notificationStatus[] = [
                    'user_id' => $user->id,
                    'status' => 'false',
                    'message' =>  $e->getMessage(),
                ];
                // Log the error or handle it as needed
                \Log::error('Notification failed for user ' . $user->id . ': ' . $e->getMessage());
            }
        }
    
        // Return a response
        return response()->json(['notification_status' => $notificationStatus], 200);
    }
}
