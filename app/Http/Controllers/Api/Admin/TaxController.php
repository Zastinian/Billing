<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaxController extends ApiController
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required|string|max:255|unique:taxes|not_in:0,Global,global',
            'percent' => 'nullable|numeric|gte:0',
            'amount' => 'nullable|numeric|gte:0',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        Tax::create([
            'country' => $request->input('country'),
            'percent' => $request->input('percent'),
            'amount' => $request->input('amount'),
        ]);

        return $this->respondJson(['success' => 'You have created a tax successfully!']);
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'country' => ['required', 'string', 'max:255', Rule::unique('taxes')->ignore($id)],
            'percent' => 'nullable|numeric|gte:0',
            'amount' => 'nullable|numeric|gte:0',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        $tax = Tax::find($id);
        $tax->country = ($tax->country === '0') ? '0' : $request->input('country');
        $tax->percent = $request->input('percent');
        $tax->amount = $request->input('amount');
        $tax->save();

        return $this->respondJson(['success' => 'You have updated the tax successfully!']);
    }
    
    public function delete($id)
    {
        $tax = Tax::find($id);

        if ($tax->country == 'Global')
            return $this->respondJson(['error' => 'Global cannot be deleted!']);
        
        $tax->delete();

        return $this->respondJson(['success' => 'You have deleted the tax successfully!']);
    }
}
