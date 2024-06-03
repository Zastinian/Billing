<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CurrencyController extends ApiController
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|size:3|unique:currencies',
            'symbol' => 'required|string',
            'rate' => 'exclude_if:default,1|required|numeric|gt:0',
            'precision' => 'required|numeric|gte:0',
            'default' => 'required|boolean',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        if ($request->input('default') === '1') {
            $default_currency = Currency::where('default', true)->first();
            $default_currency->default = false;
            $default_currency->save();
        }

        Currency::create([
            'name' => $request->input('name'),
            'symbol' => $request->input('symbol'),
            'rate' => $request->input('rate'),
            'precision' => $request->input('precision'),
            'default' => $request->input('default') === '1',
        ]);
        
        return $this->respondJson(['success' => 'You have added a currency successfully!']);
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'size:3', Rule::unique('currencies')->ignore($id)],
            'symbol' => 'required|string',
            'rate' => 'exclude_if:default,1|required|numeric|gt:0',
            'precision' => 'required|numeric|gte:0',
            'default' => 'required|boolean',
        ]);

        $currency = Currency::find($id);

        if ($request->input('default') === '1' && !$currency->default) {
            $default_currency = Currency::where('default', true)->first();
            $default_currency->default = false;
            $default_currency->save();
        } elseif ($request->input('default') === '0' && $currency->default) {
            return $this->respondJson(['error' => 'You must choose another default currency first.']);
        }

        $currency->name = $request->input('name');
        $currency->symbol = $request->input('symbol');
        $currency->rate = $request->input('default') === '1' ? 1 : $request->input('rate');
        $currency->precision = $request->input('precision');
        $currency->default = $request->input('default') === '1';
        $currency->save();

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        return $this->respondJson(['success' => 'You have updated the currency successfully!']);
    }
    
    public function delete($id)
    {
        $currency = Currency::find($id);

        if ($currency->default)
            return $this->respondJson(['error' => 'You cannot delete the default currency!']);
        
        $currency->delete();

        return $this->respondJson(['success' => 'You have deleted the currency successfully!']);
    }
}
