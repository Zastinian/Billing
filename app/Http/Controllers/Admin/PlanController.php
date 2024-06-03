<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Discount;
use App\Models\Plan;

class PlanController extends Controller
{
    public function plans()
    {
        return view('admin.plan.index');
    }
    
    public function createPlans()
    {
        return view('admin.plan.create');
    }
    
    public function plan($id)
    {
        return view('admin.plan.show', ['id' => $id, 'plan' => Plan::find($id)]);
    }
    
    public function categories()
    {
        return view('admin.category.index');
    }
    
    public function createCategory()
    {
        return view('admin.category.create');
    }
    
    public function category($id)
    {
        return view('admin.category.show', ['id' => $id, 'category' => Category::find($id)]);
    }
    
    public function addons()
    {
        return view('admin.addon.index');
    }
    
    public function createAddon()
    {
        return view('admin.addon.create');
    }
    
    public function addon($id)
    {
        return view('admin.addon.show', ['id' => $id, 'addon' => Addon::find($id)]);
    }
    
    public function discounts()
    {
        return view('admin.discount.index');
    }
    
    public function createDiscount()
    {
        return view('admin.discount.create');
    }
    
    public function discount($id)
    {
        return view('admin.discount.show', ['id' => $id, 'discount' => Discount::find($id)]);
    }
    
    public function coupons()
    {
        return view('admin.coupon.index');
    }
    
    public function createCoupon()
    {
        return view('admin.coupon.create');
    }
    
    public function coupon($id)
    {
        return view('admin.coupon.show', ['id' => $id, 'coupon' => Coupon::find($id)]);
    }
}
