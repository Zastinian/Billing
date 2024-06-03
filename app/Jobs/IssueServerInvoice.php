<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Server;
use App\Notifications\PayInvoiceNotif;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IssueServerInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The server instance.
     *
     * @var \App\Models\Server
     */
    protected $server;

    /**
     * The total due amount.
     *
     * @var float
     */
    protected $total;

    /**
     * The credit amount.
     *
     * @var float
     */
    protected $credit;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Server $server, float $total, float $credit = 0)
    {
        $this->server = $server;
        $this->total = $total;
        $this->credit = $credit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = Client::find($this->server->client_id);
        $invoice = Invoice::create([
            'client_id' => $client->id,
            'server_id' => $this->server->id,
            'total' => $this->total,
            'credit' => $this->credit,
            'payment_method' => $this->server->payment_method,
            'due_date' => $this->server->due_date,
        ]);

        $client->notify(new PayInvoiceNotif($invoice));
    }
}
