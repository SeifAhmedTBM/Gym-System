<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Membership;
use App\Models\MobileSetting;
use App\Models\Payment;
use App\Models\Pricelist;
use App\Models\Reminder;
use App\Models\Source;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\paymob_transactions;

class SubscriptionApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function validate_user(Request $request){
         $user = Lead::where('phone' , $request->phone)->first();
         if($user){
            return response()->json([
                'status' => false,
                'message' => 'This Phone is Already in use',
            
            ], 422);
         }
         else{
            return response()->json([
                'status' => true,
                'message' => 'Accepted phone number',
            
            ], 200);
         }
    }

    public function guest_subscribe(Request $request)
    {
        //
        try {
            // Begin transaction
            DB::beginTransaction();

            // Validation
            $validated = request()->validate([
                'trainer_id' => [
                    'nullable',
                    'exists:employees,id',
                ],
                'pricelist_id' => [
                    'required',
                    'exists:pricelists,id',
                ],
                'branch_id' => [
                    'required',
                    'exists:branches,id',
                ],
                'payment_amount' => [
                    'required',
                    'numeric',
                ],
                'start_date' => 'required|date_format:Y-m-d',
                'name'                 => 'string|required',
                'phone'                => 'string|required_unless:minor,yes|min:10|max:11|unique:leads,phone,NULL,id,deleted_at,NULL',
                'gender'               => 'required',
            ]);
            // create lead and transform it to member

            // get sales manager of this gym
            $sales_manager = Employee::with(['user', 'user.roles'])
                ->whereNull('deleted_at')
                ->where('branch_id', $request->branch_id)
                ->whereHas('user.roles', function($query) {
                    $query->where('id', 7);
                })
                ->latest()
                ->first();
            $branch = Branch::find($request['branch_id']);

            $user = User::create([
                'name' => $request->name,

                'email' => isset($request->email) && (!is_null($request->email)) ? $request->email : $request->phone . '@zfitness.com',

                'password' => Hash::make($request->phone) ,
                'phone'    => $request->phone,
            ]);
            $authToken = $user->createToken('auth-token')->plainTextToken;

            $last_member_code = Lead::whereType('member') ->when('branch', function ($q) use ($request) {
                $q->whereBranchId($request->branch_id);
            })->whereDeletedAt(Null)->orderBy('member_code', 'desc')->first()->member_code ?? 1;
            $source = Source::where('name','Mobile App')->latest()->first();
            if (!$source){
                $source = Source::all()->first();
            }
            $last_member_code  = $last_member_code + 001;
            $member = Lead::create([
                'name'              => $request['name'],
                'phone'             => $request['phone'],
                'national'          => $request['national'],
                'status_id'         => Status::first()->id,
                'member_code' =>    $last_member_code,
                'source_id'         => $source->id,
                'address_id'        => $request['address_id'],
                'dob'               => $request['dob'],
                'gender'            => $request['gender'],
                'sales_by_id'       => $sales_manager->user->id,
                'type'              => 'member',
                'referral_member'   => $request['referral_member'],
                'address_details'   => $request['address_details'],
                'whatsapp_number'   => $request['phone'],
                'branch_id'         => $request->branch_id,
                'sport_id'          => $request['sport_id'] ?? NULL,
                'notes'             => 'Mobile Guest Mode'. $request['notes'],
                'created_by_id'     => $sales_manager->user->id,
                'user_id' =>        $user->id,
                'invitation'        => isset($request['invitation']) ? true : false,
            ]);
            $member->leadReminder()->delete();

            $pricelist = Pricelist::find($request->pricelist_id);
            $service = $pricelist->service;

            // Handle date logic
            $startDate = Carbon::createFromFormat('Y-m-d', $request->input('start_date'));
            $expiry_type = $service->type; // 'days' or 'months'
            $expiry = $service->expiry;

            if ($expiry_type == 'days') {
                $end_date = $startDate->addDays($expiry);
            } else {
                $end_date = $startDate->addMonths($expiry);
            }

            // Create membership
            $membership = Membership::create([
                'start_date' => $request['start_date'],
                'end_date' => $end_date,
                'member_id' => $member->id,
                'trainer_id' => $request['trainer_id'],
                'service_pricelist_id' => $request->pricelist_id,
                'sales_by_id' => $member->sales_by_id,
                'notes' => 'Mobile Payments ' . $request['notes'],
                'created_at' => now(),
                'membership_status' => 'new'
            ]);
            $this->renew_call($membership);

            // Create invoice
            $invoice = Invoice::create([
                'service_fee' => $pricelist->amount,
                'discount' => 0,
                'net_amount' => $pricelist->amount,
                'membership_id' => $membership->id,
                'branch_id' => $request['branch_id'],
                'sales_by_id' => $member->sales_by_id,
                'created_by_id' => $member->user->id,
                'status' => $request->membership_fee == $request->payment_amount ? 'fullpayment' : 'partial',
                'created_at' => now(),
            ]);

            // Get default mobile settings account
            $mobile_settings = MobileSetting::first();

            // Create payment if payment amount is greater than 0
            if ($request->payment_amount > 0) {
                $payment = Payment::create([
                    'account_id' =>  $branch->online_account?$branch->online_account->id : $mobile_settings->account_id,
                    'amount' => $request->payment_amount,
                    'invoice_id' => $invoice->id,
                    'sales_by_id' => $member->sales_by_id,
                    'created_by' => $member->user->id,
                    'created_at' => now(),
                    'notes' => "Mobile Payment"
                ]);

                // Update account balance
                $payment->account->increment('balance', $payment->amount);

                // Create transaction
                Transaction::create([
                    'transactionable_type' => Payment::class,
                    'transactionable_id' => $payment->id,
                    'amount' => $request->payment_amount,
                    'account_id' =>  $branch->online_account?$branch->online_account->id : $mobile_settings->account_id,
                    'created_by' => $member->user->id,
                    'created_at' => now(),
                ]);

                paymob_transactions::create([
                    'user_id' => $member->user->id ,
                    'membership_id' => $membership->id ,
                    'transaction_amount' => $request->transaction_amount,
                    'transaction_id' => $request->transactionId,
                    'orderId'       => $request->orderId,
                    'transaction_createdAt'     => now(),
                    'paymentMethodType' => $request->paymentMethodType ,
                    'paymentMethodSubType' => $request->paymentMethodSubType,
                ]);


                // Create follow-up reminder
                Reminder::create([
                    'type' => 'follow_up',
                    'notes' => "Mobile Payment",
                    'membership_id' => $membership->id,
                    'lead_id' => $member->id,
                    'due_date' => date('Y-m-d', strtotime($membership->start_date . '+' . $membership->service_pricelist->followup_date . 'Days')),
                    'user_id' => $member->sales_by_id,
                ]);
                if ($invoice->status == 'partial') {
                    Reminder::create([
                        'type' => 'due_payment',
                        'membership_id' => $membership->id,
                        'lead_id' => $member->id,
                        'due_date' => date('Y-m-d', strtotime('+3 Days')),
                        'user_id' => $member->sales_by_id,
                    ]);

                    $member->update([
                        'status_id' => Status::firstOrCreate(
                            ['name' => 'Debt Member'],
                            ['color' => 'warning', 'default_next_followup_days' => 1, 'need_followup' => 'yes']
                        )->id
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Transaction completed successfully',
               'data'=>[
                   'member'=>$member,
                   'member_code'=>$last_member_code,
                   'membership' => $membership,
                   'invoice' => $invoice
               ] ,
               'token' => $authToken,
            ]);

        } catch (\Illuminate\Validation\ValidationException $validationException) {
            // Rollback transaction if validation fails
            DB::rollBack();

            // Return validation errors
            return response()->json([
                'message' => 'Validation error occurred.',
                'data'=> [

                'errors' => $validationException->errors(), // Returns validation errors
                ]
            ], 422);

        }
        catch (\Exception $exception) {
            // Rollback transaction in case of an error
            DB::rollBack();

            // Log the exception for debugging purposes
            \Log::error('Transaction Error: ' . $exception->getMessage(), [
                'exception' => $exception,
                'request' => $request->all(),
            ]);

            // Return a user-friendly error message
            return response()->json([
                'message' => 'An error occurred while processing the transaction. Please try again later.',
                'data'=>[
                    'error' => $exception->getMessage(),
                    'error_full' => $exception->getTrace()
                ]
            ], 500);
        }

    }
    /**
     * Store a newly created resource in storage.
     */
    public function subscribe(Request $request)
    {
        if (!auth('sanctum')->check()) {
            return response()->json([
                'message' => 'Please login first!',
                'data' => null
            ], 403);
        }
        //
        try {
            // Begin transaction
            DB::beginTransaction();

            // Validation
            $validated = request()->validate([
                'trainer_id' => [
                    'nullable',
                    'exists:employees,id',
                ],
                'pricelist_id' => [
                    'required',
                    'exists:pricelists,id',
                ],
                'branch_id' => [
                    'required',
                    'exists:branches,id',
                ],
                'payment_amount' => [
                    'required',
                    'numeric',
                ],
                'start_date' => 'required|date_format:Y-m-d',
            ]);

            // Get current member and related pricelist
            $member = auth('sanctum')->user()->lead;
            $pricelist = Pricelist::find($request->pricelist_id);
            $service = $pricelist->service;

            // Handle date logic
            $startDate = Carbon::createFromFormat('Y-m-d', $request->input('start_date'));
            $expiry_type = $service->type; // 'days' or 'months'
            $expiry = $service->expiry;

            if ($expiry_type == 'days') {
                $end_date = $startDate->addDays($expiry);
            } else {
                $end_date = $startDate->addMonths($expiry);
            }

            $branch = Branch::find($request['branch_id']);
            // Create membership
            $membership = Membership::create([
                'start_date' => $request['start_date'],
                'end_date' => $end_date,
                'member_id' => $member->id,
                'trainer_id' => $request['trainer_id'],
                'service_pricelist_id' => $request->pricelist_id,
                'sales_by_id' => $member->sales_by_id,
                'notes' => 'Mobile Payments ' . $request['notes'],
                'created_at' => now(),
                'membership_status' => 'new'
            ]);

            // Create invoice
            $invoice = Invoice::create([
                'service_fee' => $pricelist->amount,
                'discount' => 0,
                'net_amount' => $pricelist->amount,
                'membership_id' => $membership->id,
                'branch_id' => $request['branch_id'],
                'sales_by_id' => $member->sales_by_id,
                'created_by_id' => $member->user->id,
                'status' => $request->membership_fee == $request->payment_amount ? 'fullpayment' : 'partial',
                'created_at' => now(),
            ]);

            // Get default mobile settings account
            $mobile_settings = MobileSetting::first();

            // Create payment if payment amount is greater than 0

            if ($request->payment_amount > 0) {
                $payment = Payment::create([
                    'account_id' => $branch->online_account?$branch->online_account->id : $mobile_settings->account_id,
                    'amount' => $request->payment_amount,
                    'invoice_id' => $invoice->id,
                    'sales_by_id' => $member->sales_by_id,
                    'created_by' => $member->user->id,
                    'created_at' => now(),
                    'notes' => "Mobile Payment"
                ]);

                // Update account balance
                $payment->account->increment('balance', $payment->amount);

                // Create transaction
                Transaction::create([
                    'transactionable_type' => Payment::class,
                    'transactionable_id' => $payment->id,
                    'amount' => $request->payment_amount,
                    'account_id' => $branch->online_account?$branch->online_account->id : $mobile_settings->account_id,
                    'created_by' => $member->user->id,
                    'created_at' => now(),
                ]);

                paymob_transactions::create([
                    'user_id' => $member->user->id ,
                    'membership_id' => $membership->id ,
                    'transaction_amount' => $request->transaction_amount,
                    'transaction_id' => $request->transactionId,
                    'orderId'       => $request->orderId,
                    'transaction_createdAt'     => now(),
                    'paymentMethodType' => $request->paymentMethodType ,
                    'paymentMethodSubType' => $request->paymentMethodSubType,
                ]);

                // Create follow-up reminder
                Reminder::create([
                    'type' => 'follow_up',
                    'notes' => "Mobile Payment",
                    'membership_id' => $membership->id,
                    'lead_id' => $member->id,
                    'due_date' => date('Y-m-d', strtotime($membership->start_date . '+' . $membership->service_pricelist->followup_date . 'Days')),
                    'user_id' => $member->sales_by_id,
                ]);
                if ($invoice->status == 'partial') {
                    Reminder::create([
                        'type' => 'due_payment',
                        'membership_id' => $membership->id,
                        'lead_id' => $member->id,
                        'due_date' => date('Y-m-d', strtotime('+3 Days')),
                        'user_id' => $member->sales_by_id,
                    ]);

                    $member->update([
                        'status_id' => Status::firstOrCreate(
                            ['name' => 'Debt Member'],
                            ['color' => 'warning', 'default_next_followup_days' => 1, 'need_followup' => 'yes']
                        )->id
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Transaction completed successfully',
                'data'=>[
                    'member'=>$member,
                    'member_code'=>$member->member_code,
                    'membership' => $membership,
                    'invoice' => $invoice
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $validationException) {
            // Rollback transaction if validation fails
            DB::rollBack();

            // Return validation errors
            return response()->json([
                'message' => 'Validation error occurred.',
                'data'=> [

                'errors' => $validationException->errors(), // Returns validation errors
                ]
            ], 422);

        }
        catch (\Exception $exception) {
            // Rollback transaction in case of an error
            DB::rollBack();

            // Log the exception for debugging purposes
            \Log::error('Transaction Error: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data'=> [
                'request' => $request->all(),
                    ]
            ]);

            // Return a user-friendly error message
            return response()->json([
                'message' => 'An error occurred while processing the transaction. Please try again later.',
                'data'=> [
                'error' => $exception->getMessage(),
                'error_full' => $exception->getTrace()
                    ]
            ], 500);
        }


        return response()->json([]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
