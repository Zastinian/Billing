<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\KbArticle;
use App\Models\KbCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KbCategoryController extends ApiController
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'order' => 'present|numeric|gte:0',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        KbCategory::create([
            'name' => $request->input('name'),
            'order' => $request->input('order'),
        ]);

        return $this->respondJson(['success' => 'You have created a knowledge base category successfully!']);
    }
    
    public function update(Request $request, $category_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'order' => 'present|numeric|gte:0',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        $category = KbCategory::find($category_id);
        $category->name = $request->input('name');
        $category->order = $request->input('order');
        $category->save();

        return $this->respondJson(['success' => 'You have updated the knowledge base category successfully!']);
    }
    
    public function delete($category_id)
    {
        $category = KbCategory::find($category_id);

        if (KbArticle::where('category_id', $category_id)->doesntExist()) {
            $category->delete();
        } else {
            return $this->respondJson(['error' => 'You cannot delete this knowledge base category because some support articles are still inside it!']);
        }

        return $this->respondJson(['success' => 'You have deleted the knowledge base category successfully!']);
    }
}
