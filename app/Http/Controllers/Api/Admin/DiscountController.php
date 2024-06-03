<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Discount;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountController extends ApiController
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'percent_off' => 'required|integer|gte:1|lte:100',
            'is_global' => 'required|boolean',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        Discount::create([
            'name' => $request->input('name'),
            'percent_off' => $request->input('percent_off'),
            'is_global' => $request->input('is_global') === '1',
            'end_date' => $request->input('end_date'),
        ]);
        
        return $this->respondJson(['success' => 'You have created a discount successfully!']);
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'percent_off' => 'required|integer|gte:1|lte:100',
            'is_global' => 'required|boolean',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        $discount = Discount::find($id);
        $discount->name = $request->input('name');
        $discount->percent_off = $request->input('percent_off');
        $discount->is_global = $request->input('is_global') === '1';
        $discount->end_date = $request->input('end_date');
        $discount->save();

        return $this->respondJson(['success' => 'You have updated the discount successfully!']);
    }
    
    public function delete($id)
    {
        if (Plan::where('discount', $id)->exists()) {
            return $this->respondJson(['error' => 'You cannot delete this discount because some server plans are still using it!']);
        }

        $discount = Discount::find($id);
        $discount->delete();

        return $this->respondJson(['success' => 'You have deleted the discount successfully!']);
    }
}
