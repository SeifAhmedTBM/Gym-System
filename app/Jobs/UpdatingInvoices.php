<?php

namespace App\Jobs;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UpdatingInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        // to restore refunded invoices
        $invoices = Invoice::with('refund')
                ->whereHas('membership')
                ->whereHas('refund',function($q){
                    $q->whereStatus('confirmed');
                })->get();

        foreach ($invoices as $key => $invoice) 
        {
            $invoice->update([
                'status'    => 'refund'
            ]);
        }

        //to fix full payment and partial invoices
        $fix_invoices = Invoice::whereHas('payments')
                        ->withSum('payments','amount')
                        ->whereHas('membership',function($q){
                            $q->where('status','!=','refunded');
                        })->get();

        foreach ($fix_invoices as $key => $inv) 
        {
            $inv->update([
                'net_amount'    => ($inv->service_fee - $inv->discount),
                'status'        => ($inv->service_fee - $inv->discount) == $inv->payments_sum_amount ? 'fullpayment' : 'partial',
            ]);
        }

    }
}
