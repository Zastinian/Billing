<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Category;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends ApiController
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|numeric',
            'global_limit' => 'nullable|integer|gte:0',
            'per_client_limit' => 'nullable|integer|gte:0',
            'per_client_trial_limit' => 'nullable|integer|gte:0',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        Category::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'order' => $request->input('order'),
        ]);
        
        return $this->respondJson(['success' => 'You have created a server category successfully!']);
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|numeric',
            'global_limit' => 'nullable|integer|gte:0',
            'per_client_limit' => 'nullable|integer|gte:0',
            'per_client_trial_limit' => 'nullable|integer|gte:0',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        $category = Category::find($id);
        $category->name = $request->input('name');
        $category->description = $request->input('description');
        $category->order = $request->input('order');
        $category->save();

        return $this->respondJson(['success' => 'You have updated the server category successfully!']);
    }
    
    public function delete($id)
    {
        if (Plan::where('category_id', $id)->exists()) {
            return $this->respondJson(['error' => 'You cannot delete this server category because some plans are still inside it!']);
        }

        $category = Category::find($id);
        $category->delete();

        return $this->respondJson(['success' => 'You have deleted the category successfully!']);
    }
}
