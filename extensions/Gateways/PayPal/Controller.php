<?php

namespace Extensions\Gateways\PayPal;

use App\Http\Controllers\Api\ApiController;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Extension;
use App\Models\Invoice;
use Extensions\Gateways\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Srmklive\PayPal\Services\ExpressCheckout;

class Controller extends ApiController implements Gateway
{
    public static $display_name = 'PayPal';

    public static function config() {
        return require __DIR__ . '/config.php';
    }

    public static function seeder() {
        return Seeder::class;
    }

    public static function routes() {
        require __DIR__ . '/routes.php';
    }

    public static function checkout(Invoice $invoice, string $url)
    {
        $provider = new ExpressCheckout();

        $response = $provider->setCurrency(session('currency')->name)->addOptions([
            'EMAIL' => Auth::user()->email,
        ])->setExpressCheckout(self::getData($invoice, $url));

        Log::debug('[PayPal::checkout] setExpressCheckout: '.json_encode($response));
        return $response['paypal_link'];
    }

    public static function payment(Request $request, Invoice $invoice, string $url)
    {
        $provider = new ExpressCheckout();
        $token = $request->get('token');
        $response = $provider->getExpressCheckoutDetails($token);

        Log::debug('[PayPal::payment] getExpressCheckoutDetails: '.json_encode($response));
        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            $payment = $provider->doExpressCheckoutPayment(
                self::getData($invoice, $url), $token, $request->get('PayerID')
            );
            Log::debug('[PayPal::payment] doExpressCheckoutPayment: '.json_encode($payment));
            return $payment['PAYMENTINFO_0_PAYMENTSTATUS'];
        } else {
            return false;
        }
    }

    public static function show()
    {
        return view('extensions.PayPal.show');
    }

    /**
     * Update extension settings to the database
     */
    public static function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mode' => 'required|string|in:live,sandbox',
            'username' => 'required|string',
            'password' => 'required|string',
            'secret' => 'required|string',
        ]);

        if ($validator->fails())
            return ['errors' => $validator->errors()->all()];

        self::saveSetting('mode', $request->input('mode'));
        self::saveSetting('username', $request->input('username'));
        self::saveSetting('password', $request->input('password'));
        self::saveSetting('secret', $request->input('secret'));
        self::saveSetting('certificate', $request->input('certificate'));
        self::saveSetting('app_id', $request->input('app_id'));

        return ['success' => 'You have updated the PayPal extension settings successfully! Please click \'Reload Config\' above on the navigation bar to apply the changes.'];
    }

    /**
     * Additional functions
     */
    private static function saveSetting($key, $value)
    {
        $setting = Extension::where(['extension' => 'PayPal', 'key' => $key])->first();
        $setting->value = $value;
        $setting->save();
    }

    private static function getData(Invoice $invoice, $url)
    {
        $data = [];
        $total = price($invoice->total, Currency::VALUE_ONLY, Currency::where('name', Client::find($invoice->client_id)->currency)->first());
        
        if ($invoice->server_id) {
            $data['items'] = [
                [
                    'name' => 'Order Server',
                    'price' => $total,
                    'desc'  => 'Server #'.$invoice->server_id,
                    'qty' => 1,
                ]
            ];
        } elseif ($invoice->credit) {
            $data['items'] = [
                [
                    'name' => 'Add Fund',
                    'price' => $total,
                    'desc'  => 'Account Credit',
                    'qty' => 1,
                ]
            ];
        }
        
        $data['invoice_id'] = (string) Str::orderedUuid();
        $data['invoice_description'] = 'Invoice #'.$invoice->id;
        $data['return_url'] = route('api.store.payment');
        $data['cancel_url'] = $url;
        $data['total'] = $total;

        Log::debug('[PayPal::checkout] ExpressCheckout Data: '.json_encode($data));
        return $data;
    }

    public function ipn(Request $request)
    {
        $provider = new ExpressCheckout(Controller::config());

        $request->merge(['cmd' => '_notify-validate']);
        $post = $request->all();

        $response = (string) $provider->verifyIPN($post);

        return $response === 'VERIFIED';
    }
}
