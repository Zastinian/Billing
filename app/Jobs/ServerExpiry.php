<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\PlanCycle;
use App\Models\Server;
use App\Notifications\InvoiceDueNotif;
use App\Notifications\RenewServerNotif;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ServerExpiry implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $servers = Server::where('status', 0)->orWhere('status', 2)->get();

        foreach ($servers as $server) {
            $plan_cycle = PlanCycle::find($server->plan_cycle);
            $client = Client::find($server->client_id);
            $plan = Plan::find($server->plan_id);
            $trial_minutes = null;

            /**
             * Check if free trial is over
             */
            if ($plan_cycle->trial_length) {
                switch ($plan_cycle->trial_type) {
                    // Hourly
                    case 1:
                        $trial_minutes = $plan_cycle->trial_length * 60;
                        break;

                    // Daily
                    case 2:
                        $trial_minutes = $plan_cycle->trial_length * 60 * 24;
                        break;

                    // Monthly
                    case 3:
                        $trial_minutes = $plan_cycle->trial_length * 60 * 24 * 30;
                        break;

                    // Yearly
                    case 4:
                        $trial_minutes = $plan_cycle->trial_length * 60 * 24 * 30 * 12;
                        break;
                }

                if (Carbon::now()->diffInMinutes($server->created_at) >= $trial_minutes) {
                    $server->last_notif = Carbon::now();
                    $server->status = 2;
                    $server->save();

                    $client->notify(new InvoiceDueNotif(Invoice::where('server_id', $server->id)->first()));
                    SuspendServer::dispatch($server->id);
                }
            }

            // One-time servers won't expire
            if ($plan_cycle->cycle_type === 0) continue;

            $minutes = null;

            switch ($plan_cycle->cycle_type) {
                // Hourly
                case 1:
                    $minutes = $plan_cycle->cycle_length * 60;
                    break;

                // Daily
                case 2:
                    $minutes = $plan_cycle->cycle_length * 60 * 24;
                    break;

                // Monthly
                case 3:
                    $minutes = $plan_cycle->cycle_length * 60 * 24 * 30;
                    break;

                // Yearly
                case 4:
                    $minutes = $plan_cycle->cycle_length * 60 * 24 * 30 * 12;
                    break;
            }

            if (is_null($minutes)) continue;

            $invoice = Invoice::where('client_id', $client->id)->where('server_id', $server->id)->latest()->first();
            $since_last_notif = Carbon::now()->diffInMinutes($server->last_notif);

            if (Carbon::now()->lt($server->due_date)) {
                /**
                 * Server not yet expired
                 */
                if (Carbon::now()->diffInMinutes($server->due_date) < $minutes * 0.2) {
                    // Less than 20% time of the billing cycle left before the due date
                    if (!$invoice) {
                        IssueServerInvoice::dispatch($server);
                    } elseif (!$server->last_notif || $since_last_notif > $minutes * 0.05) {
                        $server->last_notif = Carbon::now();
                        $server->save();

                        // Send an email notification every more than 5% time of the billing cycle
                        $client->notify(new RenewServerNotif($server));
                    }
                }
            } else {
                /**
                 * Server already expired
                 */
                $days_before_suspend = $plan->days_before_suspend;
                $minutes_passed = Carbon::now()->diffInMinutes($server->due_date);
                $suspend_threshold = $days_before_suspend * 1440;

                if ($minutes_passed < $suspend_threshold) {
                    // Less than the configured days before suspend have passed since the due date
                    if (!$invoice) {
                        IssueServerInvoice::dispatch($server);
                    } elseif (!$server->last_notif || $since_last_notif > 1440) {
                        $server->last_notif = Carbon::now();
                        $server->save();

                        // Send an email notification every more than one day
                        $client->notify(new InvoiceDueNotif(Invoice::where('server_id', $server->id)->first()));
                    }
                }

                // Apply late fee
                if (!$invoice->late_fee) {
                    $invoice->late_fee = $plan_cycle->late_fee;
                    $invoice->save();
                }

                // Check for renew price and client credit
                if ($client->credit >= $plan_cycle->renew_price) {
                    $client->credit -= $plan_cycle->renew_price;
                    $client->save();

                    $server->due_date = Carbon::now()->addMinutes($minutes);
                    $server->status = 0; // Set status back to active
                    $server->save();
                } else {
                    if (!$server->last_notif || $since_last_notif > 1440) {
                        $server->last_notif = Carbon::now();
                        $server->status = 2;
                        $server->save();

                        $client->notify(new InvoiceDueNotif(Invoice::where('server_id', $server->id)->first()));
                    }
                }

                if ($server->status === 2 && $minutes_passed >= $suspend_threshold) {
                    // Suspend server after the configured days have passed since the due date
                    $server->status = 2;
                    $server->save();
                    SuspendServer::dispatch($server->id);
                }

                if ($server->status === 2 && $minutes_passed >= ($suspend_threshold + $plan->days_before_delete * 1440)) {
                    // Delete server after the specified days before delete have passed since the suspension date
                    $server->status = 3;
                    $server->save();
                    DeleteServer::dispatch($server->id);
                }
            }
        }
    }
}
