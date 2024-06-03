<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\Credit;
use App\Models\Currency;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\Server;
use App\Notifications\InvoicePaidNotif;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\CreateServer;
use App\Jobs\UpdateServer;

class InvoicePaid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The server instance.
     *
     * @var \App\Models\Invoice
     */
    protected $invoice;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->invoice->paid = true;
        $this->invoice->save();

        $client = Client::find($this->invoice->client_id);
        $client->notify(new InvoicePaidNotif($this->invoice));

        if ($this->invoice->server_id) {
            $server = Server::find($this->invoice->server_id);

            if (is_null($server->server_id) && is_null($server->identifier)) {
                CreateServer::dispatch($server)->onQueue('high');
            } else {
                UpdateServer::dispatch($server)->onQueue('high');
            }

            if ($this->invoice->credit > 0) {
                $client->credit -= $this->invoice->credit;
                $client->save();

                Credit::create([
                    'client_id' => $client->id,
                    'details' => 'Invoice #'.$this->invoice->id,
                    'change' => -$this->invoice->credit,
                    'balance' => $client->credit,
                ]);
            } 

            if ($this->invoice->total > 0)
                Income::create([
                    'item' => 'Server #'.$server->id,
                    'price' => $this->invoice->total,
                    'client_id' => $client->id,
                ]);
        } elseif ($this->invoice->credit) {
            $client->credit += $this->invoice->credit;
            $client->save();

            Credit::create([
                'client_id' => $client->id,
                'details' => 'Invoice #'.$this->invoice->id,
                'change' => $this->invoice->credit,
                'balance' => $client->credit,
            ]);

            Income::create([
                'item' => price($this->invoice->total, Currency::SYMBOL_VALUE_NAME).' Credit',
                'price' => $this->invoice->total,
                'client_id' => $client->id,
            ]);
        }
    }
}
