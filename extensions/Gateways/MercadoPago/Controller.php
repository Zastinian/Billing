<?php

namespace Extensions\Gateways\MercadoPago;

use App\Http\Controllers\Api\ApiController;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Extension;
use App\Models\Invoice;
use App\Jobs\InvoicePaid;
use Extensions\Gateways\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;

class Controller extends ApiController implements Gateway
{
    public static $display_name = 'MercadoPago';

    public static function config() {
        return require __DIR__ . '/config.php';
    }

    public static function seeder() {
        return Seeder::class;
    }

    public static function routes(){
        require __DIR__ . '/routes.php';
    }

    public static function checkout(Invoice $invoice, string $url)
    {
        self::authenticate();
        $preference = self::createPaymentPreference($invoice, $url);

        if ($preference) {
            return $preference->init_point;
        }

        return null;
    }

    public static function payment(Request $request, Invoice $invoice, string $url)
    {
        self::authenticate();
        $client = new PaymentClient();

        try {
            $payment = $client->get($request->get('payment_id'));
            return $payment->status === 'approved';
        } catch (MPApiException $e) {
            Log::error('[MercadoPago::payment] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function show()
    {
        return view('extensions.MercadoPago.show');
    }

    public static function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required|string',
            'enabled' => 'required|string|in:0,1',
        ]);

        if ($validator->fails())
            return ['errors' => $validator->errors()->all()];

        self::saveSetting('access_token', $request->input('access_token'));
        self::saveSetting('enabled', $request->input('enabled'));

        return ['success' => 'You have updated the MercadoPago extension settings successfully! Please click \'Reload Config\' above on the navigation bar to apply the changes.'];
    }

    private static function saveSetting($key, $value)
    {
        $settings = Extension::where(['extension' => 'MercadoPago', 'key' => $key])->first();
        $settings->value = $value;
        $settings->save();
    }

    protected static function authenticate()
    {
        $config = self::config();
        MercadoPagoConfig::setAccessToken($config['access_token']);
    }

    private static function createPaymentPreference(Invoice $invoice, $url)
    {
        $client = new PreferenceClient();

        $request = self::getPreferenceData($invoice, $url);

        try {
            $preference = $client->create($request);
            Log::debug('[MercadoPago::createPaymentPreference] Preference: ' . json_encode($preference));
            return $preference;
        } catch (MPApiException $e) {
            Log::error('[MercadoPago::createPaymentPreference] Error: ' . $e->getMessage());
            return null;
        }
    }

    private static function getPreferenceData(Invoice $invoice, $url)
    {
        $data = [];
        $total = price($invoice->total, Currency::VALUE_ONLY, Currency::where('name', Client::find($invoice->client_id)->currency)->first());

        if ($invoice->server_id) {
            $item = [
                'title' => 'Order Server',
                'quantity' => 1,
                'currency_id' => session('currency')->name,
                'unit_price' => $total,
                'description' => 'Server #' . $invoice->server_id,
            ];
        } elseif ($invoice->credit) {
            $item = [
                'title' => 'Add Fund',
                'quantity' => 1,
                'currency_id' => session('currency')->name,
                'unit_price' => $total,
                'description' => 'Account Credit',
            ];
        }

        $config = self::config();

        $data = [
            'items' => [$item],
            'payer' => [
                'email' => Auth::user()->email,
            ],
            'back_urls' => [
                'success' => route('api.store.payment'),
                'failure' => $url,
                'pending' => $url,
            ],
            'auto_return' => 'approved',
            'external_reference' => $invoice->id,
            'notification_url' => config('app.url') . $config['notification_url'],
        ];

        Log::debug('[MercadoPago::getPreferenceData] Preference Data: ' . json_encode($data));
        return $data;
    }

    public function ipn(Request $request)
    {
        self::authenticate();
        $client = new PaymentClient();

        try {
            if (!$request->get('data')['id']) return false;
            $payment = $client->get($request->get('data')['id']);
            Log::debug('[MercadoPago::ipn] Payment: ' . json_encode($payment));
            if ($payment->status === 'approved') {
                $invoice = Invoice::where('id', $payment->external_reference)->first();
                if (!$invoice->paid) {
                    InvoicePaid::dispatchSync($invoice);
                }
                return true;
            }
            return false;
        } catch (MPApiException $e) {
            Log::error('[MercadoPago::ipn] Error: ' . $e->getMessage());
            return false;
        }
    }
}
