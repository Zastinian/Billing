<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Coupon;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CouponController extends ApiController
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:coupons',
            'percent_off' => 'required|numeric|gt:0|lte:100',
            'one_time' => 'required|boolean',
            'global_limit' => 'nullable|integer|gte:0',
            'per_client_limit' => 'nullable|integer|gte:0',
            'is_global' => 'required|boolean',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        Coupon::create([
            'code' => $request->input('code'),
            'percent_off' => $request->input('percent_off'),
            'one_time' => $request->input('one_time') === '1',
            'global_limit' => $request->input('global_limit'),
            'per_client_limit' => $request->input('per_client_limit'),
            'is_global' => $request->input('is_global') === '1',
            'end_date' => $request->input('end_date'),
        ]);

        return $this->respondJson(['success' => 'You have created a coupon successfully!']);
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:255', Rule::unique('coupons')->ignore($id)],
            'percent_off' => 'required|numeric|gt:0|lte:100',
            'one_time' => 'required|boolean',
            'global_limit' => 'nullable|integer|gte:0',
            'per_client_limit' => 'nullable|integer|gte:0',
            'is_global' => 'required|boolean',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        $coupon = Coupon::find($id);
        $coupon->code = $request->input('code');
        $coupon->percent_off = $request->input('percent_off');
        $coupon->one_time = $request->input('one_time') === 1;
        $coupon->global_limit = $request->input('global_limit');
        $coupon->per_client_limit = $request->input('per_client_limit');
        $coupon->is_global = $request->input('is_global') === 1;
        $coupon->end_date = $request->input('end_date');
        $coupon->save();
        
        return $this->respondJson(['success' => 'You have updated the coupon successfully!']);
    }
    
    public function delete($id)
    {
        foreach (Plan::where('coupons', '!=', '[]')->orWhereNotNull('coupons')->get() as $plan) {
            if (in_array($id, json_decode($plan->coupons, true))) {
                return $this->respondJson(['error' => 'You cannot delete this coupon because some server plans are still using it!']);
            }
        }

        $coupon = Coupon::find($id);
        $coupon->delete();

        return $this->respondJson(['success' => 'You have deleted the coupon successfully!']);
    }
}
