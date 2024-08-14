<?php

namespace App\Imports\DataMigration;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Role;
use App\Models\User;
use App\Models\Loan;
use App\Models\Employee;
use App\Models\Source;
use App\Models\Status;
use App\Models\Account;
use App\Models\Address;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ExpensesCategory;
use App\Models\Expense;
use App\Models\Service;
use App\Models\Pricelist;
use App\Models\Membership;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\MembershipAttendance;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Models\Sport;

class MembersImport implements ToCollection , WithHeadingRow , WithChunkReading
{
    /**
     * Chunking Uploaded Data
     */
    public function chunkSize(): int
    {
        return 1;
    }

    /**
     * Batching Uploaded Data
     */
    public function batchSize() : int 
    {
        return 1;
    }


    /**
    * @param Collection $collection
    */



    /////////////////////////////////////
    // General Import (Standard)
    ////////////////////////////////////


    // public function collection(Collection $collection)
    // {
    //     foreach($collection as $row) {
            
    //         $lead = Lead::firstOrCreate([
    //             'member_code'           => $row['member_code']
    //         ],
    //         [
    //             'name'                  => $row['member_name'],
    //             'member_code'           => $row['member_code'],
    //             'phone'                 => $row['phone'] ?? NULL,
    //             // 'address_details'       => $row['address_details'],
    //             // 'whatsapp'              => $row['whatsapp'],
    //             'address_id'            => Address::first()->id,
    //             // 'national'              => $row['national'],
    //             // 'dob'                   => date('Y-m-d', strtotime($row['dob'])),
    //             'gender'                => $row['gender'] != NULL ? strtolower($row['gender']) : 'male',
    //             'sales_by_id'           => User::whereName($row['sales_by'])->first()->id ?? 2,
    //             'created_by_id'         => User::whereName($row['sales_by'])->first()->id ?? 2,
    //             'type'                  => 'member',
    //         ]);

    //         $membership = Membership::create([
    //             'member_id'                 => $lead->id,
    //             'start_date'                => date('Y-m-d', strtotime($row['start_date'])),
    //             'end_date'                  => date('Y-m-d', strtotime($row['end_date'])),
    //             'created_at'                => date('Y-m-d', strtotime($row['created_at'])),
    //             'service_pricelist_id'      => Pricelist::whereName($row['plan_name'])->first()->id ?? dd($row['plan_name']),
    //             'sales_by_id'               => User::whereName($row['sales_by'])->first()->id ?? 2,
    //             'created_by_id'             => User::whereName($row['sales_by'])->first()->id ?? 2,
    //             'trainer_id'                => User::whereName($row['trainer'])->first()->id ?? null,
    //             'status'                    => date('Y-m-d', strtotime($row['end_date'])) > date('Y-m-d') ? 'current' : 'expired',
    //             // 'notes'                     => $row['notes']
    //         ]);

    //         // if($row['attendances_count'] != NULL) {
    //         //     for($i = 0; $i < $row['attendances_count']; $i++) {
    //         //         MembershipAttendance::create([
    //         //             'membership_id'     => $membership->id,
    //         //             'sign_in'           => date('H:i:s'),
    //         //             'sign_out'          => date('H:i:s' , strtotime('+1 Hours')),
    //         //             'membership_status' => $membership->status
    //         //         ]);
    //         //     }
    //         // }

    //         // $invoice = Invoice::firstOrCreate([
    //         //     'id'                => $row['invoice_number'],
    //         // ],[
    //         //     'id'                => $row['invoice_number'],
    //         //     'membership_id'     => $membership->id,
    //         //     'discount'          => $row['discount_amount'] ?? 0,
    //         //     'discount_notes'    => $row['discount_notes'],
    //         //     'service_fee'       => Pricelist::whereName($row['plan_name'])->first()->amount,
    //         //     'net_amount'        => Pricelist::whereName($row['plan_name'])->first()->amount - $row['discount_amount'],
    //         //     'sales_by_id'       => User::whereName($row['sales_by'])->first()->id ?? 2,
    //         //     'created_by_id'     => User::whereName($row['sales_by'])->first()->id ?? 2,
    //         //     'status'            => 'fullpayment',
    //         //     'created_at'        => $row['created_at'],
    //         // ]);


    //         $invoice = Invoice::firstOrCreate([
    //             'id'                => $row['invoice_number'],
    //         ],[
    //             'id'                => $row['invoice_number'],
    //             'membership_id'     => $membership->id,
    //             'discount'          => $row['discount_amount'] ?? 0,
    //             'discount_notes'    => $row['discount_notes'],
    //             'service_fee'       => Pricelist::whereName($row['plan_name'])->first()->amount,
    //             'net_amount'        => $row['net_amount'],
    //             'sales_by_id'       => User::whereName($row['sales_by'])->first()->id ?? 2,
    //             'created_by_id'     => User::whereName($row['sales_by'])->first()->id ?? 2,
    //             'status'            => 'fullpayment',
    //             'created_at'        => $row['created_at'],
    //         ]);

    //         $payment = Payment::create([
    //             'invoice_id'        => $invoice->id,
    //             'amount'            => $row['payment_amount'],
    //             'created_at'        => $row['created_at'],
    //             'sales_by_id'       => User::whereName($row['sales_by'])->first()->id ?? 2,
    //             'created_by_id'     => User::whereName($row['sales_by'])->first()->id ?? 2,
    //             'account_id'        => Account::whereName(strtolower($row['mode']))->first()->id,
    //         ]);
            
    //         $transaction = Transaction::create([
    //             'transactionable_type'  => 'App\\Models\\Payment',
    //             'transactionable_id'    => $payment->id,
    //             'amount'                => $payment->amount,
    //             'account_id'            => $payment->account_id,
    //             'created_by'            => $payment->created_by_id,
    //             'created_at'            => $payment->created_at
    //         ]);

    //     }
    // }

    public function collection(Collection $collection)
    {
        foreach($collection as $row) {
            

            // $sales_by = User::whereName($row['sales_by'])->first();

            // $lead = Lead::firstOrCreate([
            //     'phone'           => $row['phone']
            // ],
            // [
            //     'member_code'           => $row['member_code'],
            //     'name'                  => $row['name'],
            //     'address_details'       => '',
            //     'phone'                 => $row['phone'] != NULL ? $row['phone'] : '01xxxxxxxxx',
            //     // 'whatsapp'              => $row['whatsapp'],
            //     // 'national'              => $row['national'],
            //     'branch_id'             => $row['branch_id'],
            //     'dob'                   => '2000-01-01',
            //     'source_id'             => Source::whereName($row['source'])->first()->id ?? Source::firstOrCreate(['name' => $row['source']])->id,
            //     'status_id'             => Status::whereName($row['status'])->first()->id ?? Status::first()->id,
            //     'address_id'            => Address::first()->id ?? NULL,
            //     'gender'                => $row['gender'] != NULL ? strtolower($row['gender']) : 'male',
            //     'type'                  => 'member',
            //     'notes'                 => '',
            //     'sales_by_id'           => $sales_by->id ?? User::first()->id,
            //     'created_by_id'         => $sales_by->id ?? User::first()->id,
            //     'created_at'            => $row['created_at'],
            //     // 'created_at'            => Date::excelToDateTimeObject($row['start_date'])->format('Y-m-d'),
            // ]);


            ////
            // Expenses Import
            ///
            // $expense_category = ExpensesCategory::whereName($row['expense_category'])->first();
            // $created_by = User::whereName($row['created_by'])->first();

            // $expense = Expense::create([
            //     'name' => $row['name'],
            //     'date' => $row['date'],
            //     'amount' => $row['amount'],
            //     'note' => '',
            //     'expenses_category_id' => $expense_category ? $expense_category->id : ExpensesCategory::first()->id,
            //     'account_id' => $row['account'],
            //     'created_at' => $row['date'],
            //     'created_by_id' => $created_by ? $created_by->id : User::first()->id,
            // ]);
    
            // $expense->account->balance = $expense->account->balance - $expense->amount;
            // $expense->account->save();
    
            // $transaction = Transaction::create([
            //     'transactionable_type' => 'App\\Models\\Expense',
            //     'transactionable_id' => $expense->id,
            //     'amount' => $expense->amount,
            //     'account_id' => $expense->account_id,
            //     'created_at' => $row['date'],
            //     'created_by' => $created_by ? $created_by->id : User::first()->id,
            // ]);
                

          
            $miliseconds = ($row['date'] - (25567 + 2)) * 86400 * 1000;
            $seconds = $miliseconds / 1000;
            $created_at =  date("Y-m-d", $seconds);

            $created_by = User::whereName($row['created_by'])->first();
            $employee = Employee::whereName($row['employee'])->first();

            $loan = Loan::create([
                'employee_id'   => $employee->id,
                'name'          => $row['name'],
                'amount'        => $row['amount'],
                'account_id'    => $row['account_id'],
                'created_at'    => $created_at,
                'created_by_id' => $created_by->id,
            ]);
            
    
            $transaction = Transaction::create([
                'transactionable_type' => 'App\\Models\\Loan',
                'transactionable_id' => $loan->id,
                'amount' => $loan->amount,
                'account_id' => $loan->account_id,
                'created_at' => $loan->created_at,
                'created_by' => $created_by->id,
            ]);
            
            $loan->account->balance = $loan->account->balance - $loan->amount;
            $loan->account->save();

            // $membership = Membership::firstOrCreate([
            //     'member_id'                 => $lead->id,
            //     'service_pricelist_id'      => Pricelist::whereName($row['plan_name'])->first()->id ?? dd($row['plan_name']),
            //     'start_date'                => $row['start_date'],
            //     'end_date'                  => $row['end_date'],
            // ],[
            //     'member_id'                 => $lead->id,
            //     'start_date'                => date('Y-m-d', strtotime($row['start_date'])),
            //     'end_date'                  => date('Y-m-d', strtotime($row['end_date'])),
            //     'created_at'                => $row['start_date'],
            //     // 'sport_id'                  => Sport::whereName($row['sport_name'])->firstOrCreate(['name' => $row['sport_name']])->id,
            //     'service_pricelist_id'      => Pricelist::whereName($row['plan_name'])->first()->id ?? dd($row['plan_name']),
            //     'trainer_id'                => NULL,
            //     'status'                    => date('Y-m-d', strtotime($row['end_date'])) > date('Y-m-d') ? 'current' : 'expired',
            //     'sales_by_id'               => User::first()->id,
            //     'created_by_id'             => User::first()->id,
            // ]);

            // if($row['no_of_attendances'] != NULL) {
            //     for($i = 0; $i < $row['no_of_attendances']; $i++) {
            //         MembershipAttendance::create([
            //             'membership_id'     => $membership->id,
            //             'sign_in'           => date('H:i:s'),
            //             'sign_out'          => date('H:i:s' , strtotime('+1 Hours')),
            //             'membership_status' => $membership->status
            //         ]);
            //     }
            // }
    
            // $invoice = Invoice::create([
            //     'membership_id'     => $membership->id,
            //     'discount'          => 0,
            //     'discount_notes'    => null,
            //     'service_fee'       => Pricelist::whereName($row['plan_name'])->first()->amount,
            //     'net_amount'        => Pricelist::whereName($row['plan_name'])->first()->amount,
            //     'status'            => 'fullpayment',
            //     'created_at'        =>  $row['start_date'],
            //     'sales_by_id'       => User::first()->id,
            //     'created_by_id'     => User::first()->id,
            // ]);
    
            // $payment = Payment::create([
            //     'invoice_id'        => $invoice->id,
            //     'amount'            => Pricelist::whereName($row['plan_name'])->first()->amount,
            //     'account_id'        => Account::first()->id,
            //     'created_at'        => $row['start_date'],
            //     'sales_by_id'       => User::first()->id,
            //     'created_by_id'     => User::first()->id,
            // ]);
            
            // $transaction = Transaction::create([
            //     'transactionable_type'  => 'App\\Models\\Payment',
            //     'transactionable_id'    => $payment->id,
            //     'amount'                => $payment->amount,
            //     'account_id'            => $payment->account_id,
            //     'created_by'            => $payment->created_by_id,
            //     'created_at'            => $payment->created_at
            // ]);
            // ini_set('max_execution_time', 300);
            // if (isset($row['member_code'])) 
            // {
            //     $member = Lead::with(['memberships','memberships.invoice','memberships.invoice.payments'])->whereMemberCode($row['member_code'])->first();

              
            //     if(User::whereName($row['sales_by'])->first()){
            //         $sales_by = User::whereName($row['sales_by'])->first()->id;
            //     }else{
            //         dd($row['sales_by']);
            //     }
            //     foreach ($member->memberships as $key => $membership) 
            //     {
            //         $membership->update([
            //             'sales_by_id'       => $sales_by
            //         ]);

            //         $membership->invoice()->first()->update([
            //             'sales_by_id'       => $sales_by
            //         ]);

            //         foreach ($membership->invoice->payments as $key => $payment) 
            //         {
            //             $payment->update([
            //                 'sales_by_id'    => $sales_by
            //             ]);
            //         }
            //     }

            //     $member->update([
            //         'sales_by_id'           => $sales_by
            //     ]);
            // }
    
        }
    }
}