<?php

namespace App\Imports\DataMigration;

use App\Models\User;
use App\Models\Lead;
use App\Models\Action;
use App\Models\Source;
use App\Models\Status;
use App\Models\Account;
use App\Models\Address;
use App\Models\Expense;
use App\Models\Service;
use App\Models\Pricelist;
use App\Models\ServiceType;
use App\Models\MemberStatus;
use App\Models\RefundReason;
use App\Models\ServiceOption;
use App\Models\ExpensesCategory;
use App\Models\ServiceOptionsPricelist;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MasterDataImport implements ToCollection , WithHeadingRow, WithChunkReading, WithBatchInserts
{

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach($collection as $row) {
            // if(isset($row['actions']) && !is_null($row['actions'])) {
            //     Action::create(['name' => $row['actions']]);
            // }

            // if(isset($row['statuses']) && !is_null($row['statuses'])) {
            //     Status::create([
            //         'name'                       => $row['statuses'],
            //         'color'                      => $row['colors'],
            //         'default_next_followup_days' => $row['default_next_followup_days'],
            //         'next_followup'              => $row['next_followup'],
            //         'need_followup'              => $row['need_followup'] == '=TRUE()' ? 'yes' : 'no'
            //     ]);
            // }

            // if(isset($row['sources']) && !is_null($row['sources'])) {
            //     Source::create(['name' => $row['sources']]);
            // }

            // if(isset($row['addresses']) && !is_null($row['addresses'])) {
            //     Address::create(['name' => $row['addresses']]);
            // }

            // if(isset($row['expenses_categories']) && !is_null($row['expenses_categories'])) {
            //     ExpensesCategory::create(['name' => $row['expenses_categories']]);
            // }

            // if(isset($row['service_options']) && !is_null($row['service_options'])) {
            //     ServiceOption::create(['name' => $row['service_options']]);
            // }

            // if(isset($row['refund_reasons']) && !is_null($row['refund_reasons'])) {
            //     RefundReason::create(['name' => $row['refund_reasons']]);
            // }

            // // Service Types
            // if(isset($row['service_type_name']) && !is_null($row['service_type_name'])) {
            //     ServiceType::create([
            //         'name'              => $row['service_type_name'],
            //         'description'       => $row['service_type_description'],
            //         'session_type'      => $row['service_type_session_type']
            //     ]);
            // }

            // // Services
            // if(isset($row['service_name']) && !is_null($row['service_name'])) {
            //     Service::create([
            //         'name'              => $row['service_name'],
            //         'expiry'            => $row['service_expiry'],
            //         'service_type_id'   => ServiceType::whereName($row['service_service_type'])->first()->id,
            //         'status'            => $row['service_status'],
            //         'coach'             => $row['service_with_coach'] == '=TRUE()' ? 1 : 0,
            //         'sales_commission'  => $row['service_has_commission'] == '=TRUE()' ? 1 : 0
            //     ]);
            // }

            // Pricelists
            // if(isset($row['pricelist_name']) && !is_null($row['pricelist_name'])) {
            //     $pricelist = Pricelist::firstOrCreate([
            //         'name'      => $row['pricelist_name']
            //     ],[
            //         'status'          => 'active',
            //         'amount'          => $row['pricelist_amount'],
            //         'pricelist_category_id'      => service::where('name',)
            //         'session_count'     => $row['pricelist_session_count'],
            //         'freeze_count'      => $row['pricelist_freeze_count'],
            //         'fullday'           => 'true',
            //         'upgrade_from'      => $row['upgrade_from'] != NULL ? $row['upgrade_from'] : 0,
            //         'upgrade_to'        => $row['upgrade_to'] != NULL ? $row['upgrade_to'] : 0,
            //         'expiring_session'  => $row['expiring_session'],
            //         'expiring_date'     => $row['expiring_days']
            //     ]);

            //     $first_service_option = ServiceOption::firstOrCreate(['name' => 'Body Comp']);
            //     ServiceOptionsPricelist::create([
            //         'service_option_id' => $first_service_option->id,
            //         'pricelist_id'      => $pricelist->id,
            //         'count'             => $row['service_option_body_comp'] != NULL ? $row['service_option_body_comp'] : 0
            //     ]);
            //     $second_service_option = ServiceOption::firstOrCreate(['name' => 'Free Invitations']);
            //     ServiceOptionsPricelist::create([
            //         'service_option_id' => $second_service_option->id,
            //         'pricelist_id'      => $pricelist->id,
            //         'count'             => $row['service_option_free_invitation'] != NULL ? $row['service_option_free_invitation'] : 0
            //     ]);
            // }
          
            // $leads = Lead::where('phone','=',$row['phone'])->get();
         
            // if(count($leads)){
            //     foreach($leads as $lead){
            //         $check_sales_by = User::where('name','=',$row['sales_by'])->get();
                  
            //         if(count($check_sales_by) > 0){
            //             $sales_by = User::where('name','=',$row['sales_by'])->first();
            //             if($sales_by){
            //                 $lead->sales_by_id = $sales_by->id;
            //                 $lead->save();
            //             }
            //         }
            //     }
            // }

        }
    }

    public function chunkSize(): int
    {
        return 50;
    }

    public function batchSize(): int
    {
        return 50;    
    }
}
