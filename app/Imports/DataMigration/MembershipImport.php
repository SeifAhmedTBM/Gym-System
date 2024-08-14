<?php

namespace App\Imports\DataMigration;

use App\Models\Lead;
use App\Models\Role;
use App\Models\User;
use App\Models\Source;
use App\Models\Status;
use App\Models\Account;
use App\Models\Address;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Pricelist;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class MembershipImport implements ToCollection , WithHeadingRow, WithChunkReading, WithBatchInserts
{
    /**
     * Batch Size
     */
    public function batchSize(): int
    {
        return 1;
    }
    /**
     * Chunk Size
     */
    public function chunkSize(): int
    {
        return 1;
    }

    /**
    * @param Collection $collection
    */
    
    // public function collection(Collection $collection)
    // {
    //     // foreach($collection as $row) {
    //     //     $lead = Lead::where('name', $row['name'])->first();
    //     //     if($lead && $row['phone'] != NULL) {
    //     //         $lead->update(['phone' => $row['phone']]);
    //     //     }
    //     // }

    //     foreach ($collection as $key => $row) {
    //         ini_set('max_execution_time', 300);
    //         DB::transaction(function() use ($row)
    //         {
    //             $member = Lead::whereType('member')->whereMemberCode($row['member_code'])->first();
    //             $membership = Membership::firstOrCreate([
    //                 'member_id'             => $member->id,
    //                 'start_date'            => date('Y-m-d',strtotime($row['start_date'])),
    //                 'end_date'              => date('Y-m-d',strtotime($row['end_date'])),
    //                 'service_pricelist_id'  => Pricelist::whereName($row['plan_name'])->first()->id,
    //             ],[
    //                 'member_id'             => $member->id,
    //                 'start_date'            => date('Y-m-d',strtotime($row['start_date'])),
    //                 'end_date'              => date('Y-m-d',strtotime($row['end_date'])),
    //                 'trainer_id'            => User::whereName($row['trainer'])->first()->id ?? NULL,
    //                 'status'                => 'pending',
    //                 'sales_by_id'           => User::whereName($row['sales_by'])->first()->id ?? User::first()->id,
    //                 'service_pricelist_id'  => Pricelist::whereName($row['plan_name'])->first()->id,
    //                 'created_at'            => $row['created_at'],
    //                 'updated_at'            => $row['created_at'],
    //             ]);

    //             $invoice = Invoice::firstOrCreate([
    //                 'id'                    => $row['invoice_number']
    //             ],[
    //                 'id'                    => $row['invoice_number'],
    //                 'membership_id'         => $membership->id,
    //                 'net_amount'            => 0,
    //                 'service_fee'            => Pricelist::whereName($row['plan_name'])->first()->amount,
    //                 'discount'              => $row['discount_amount'] ?? 0,
    //                 'discount_notes'        => $row['discount_notes'],
    //                 'sales_by_id'           => User::whereName($row['sales_by'])->first()->id ?? User::first()->id,
    //                 'created_by_id'         => User::whereName($row['sales_by'])->first()->id ?? User::first()->id,
    //                 'service_pricelist_id'  => Pricelist::whereName($row['plan_name'])->first()->amount,
    //                 'status'                => 'fullpayment',
    //                 'created_at'            => $row['created_at'],
    //                 'updated_at'            => $row['created_at'],
    //             ]);

    //             $payment = Payment::create([
    //                 'invoice_id'            => $invoice->id,
    //                 'account_id'            => Account::whereName($row['mode'])->first()->id,
    //                 'account_id'            => Account::whereName($row['mode'])->first()->id,
    //                 'sales_by_id'           => User::whereName($row['sales_by'])->first()->id ?? User::first()->id,
    //                 'amount'                => $row['payment_amount'],
    //                 'created_at'            => $row['created_at'],
    //                 'updated_at'            => $row['created_at'],
    //             ]);

    //             $invoice->update([
    //                 'net_amount'            => Payment::whereInvoiceId($invoice->id)->sum('amount'),
    //             ]);
    //         });
    //     }
    // }

    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) {
            $name = explode('-',$row['name'])[0];
            $phone = explode('-',$row['name'])[1];

            $service = Service::whereName($row['package'])->first();
            
            if (isset($name) && isset($phone))
            {
                $member = Lead::firstOrCreate([
                    'phone'                 => $phone
                ],[
                    'name'                  => $name,
                    'phone'                 => $phone ?? '010xxxxxxxxx',
                    // 'whatsapp_number'       => $row['mobile'],
                    'member_code'           => $row['id'] ?? $row['id'],
                    // 'national_id'           => $row['national_id'],
                    'gender'                => 'male',
                    'sales_by_id'           => User::first()->id,
                    'source_id'             => Source::first()->id,
                    // 'status_id'             => Status::first()->id,
                    // 'address_id'            => Address::first()->id,
                    // 'dob'                   => date('Y-m-d',strtotime($row['bod'])) ?? date('Y-m-d'),
                    // 'notes'                 => $row['notes'] ?? Null,
                    'created_by_id'         => User::first()->id,
                    'created_at'            => date('Y-m-d'),
                    'type'                  => 'member'
                ]); 

                $end_date = Date::excelToDateTimeObject(floatval($row['end_date']))->format('Y-m-d');

                $membership = Membership::create([
                    'member_id'             => $member->id,
                    'start_date'            => Carbon::parse($end_date)->subMonths($service->expiry)->format('Y-m-d'),
                    'end_date'              => $end_date,
                    'trainer_id'            => NULL,
                    'status'                => 'current',
                    'sales_by_id'           => User::first()->id,
                    'service_pricelist_id'  => Pricelist::whereName($row['package'])->first()->id,
                    'created_at'            => date('Y-m-d'),
                ]);

                $invoice = Invoice::create([
                    'membership_id'         => $membership->id,
                    'net_amount'            => Pricelist::whereName($row['package'])->first()->amount,
                    'service_fee'           => Pricelist::whereName($row['package'])->first()->amount,
                    'discount'              => 0,
                    'discount_notes'        => NULL,
                    'sales_by_id'           => User::first()->id,
                    'created_by_id'         => User::first()->id,
                    'status'                => 'fullpayment',
                    'created_at'            => date('Y-m-d'),
                ]);

                $payment = Payment::create([
                    'invoice_id'            => $invoice->id,
                    'account_id'            => Account::first()->id,
                    'sales_by_id'           => User::first()->id,
                    'amount'                => Pricelist::whereName($row['package'])->first()->amount,
                    'created_at'            => date('Y-m-d'),
                ]);
            }
        }
    }
}
