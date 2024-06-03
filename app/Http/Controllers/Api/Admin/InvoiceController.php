<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Jobs\InvoicePaid;
use App\Models\Invoice;

class InvoiceController extends ApiController
{    
    public function paid($id)
    {
        $invoice = Invoice::find($id);
        $invoice->paid = true;
        $invoice->save();

        InvoicePaid::dispatch($invoice)->onQueue('high');

        /*if ($server = Server::find($invoice->server_id)) {
            $plan_cycle = PlanCycle::find($server->plan_cycle);
            $minutes = null;

            switch ($plan_cycle->cycle_type) {
                case 1:
                    $minutes = $plan_cycle->cycle_length * 60;
                    break;

                case 2:
                    $minutes = $plan_cycle->cycle_length * 60 * 24;
                    break;

                case 3:
                    $minutes = $plan_cycle->cycle_length * 60 * 24 * 30;
                    break;

                case 4:
                    $minutes = $plan_cycle->cycle_length * 60 * 24 * 30 * 12;
                    break;
            }
            
            if ($minutes) {
                $server->due_date = Carbon::parse($server->due_date)->addMinutes($minutes);
                $server->save();
            }
        }*/

        return $this->respondJson(['success' => 'You have marked the invoice as paid!']);
    }
}
