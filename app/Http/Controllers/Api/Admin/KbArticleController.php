<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\KbArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KbArticleController extends ApiController
{
    public function create(Request $request, $category_id)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'order' => 'present|numeric|gte:0',
            'content' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        KbArticle::create([
            'category_id' => $category_id,
            'subject' => $request->input('subject'),
            'content' => $request->input('content'),
            'order' => $request->input('order'),
        ]);

        return $this->respondJson(['success' => 'You have created a support article successfully!']);
    }
    
    public function update(Request $request, $category_id, $article_id)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'order' => 'present|numeric|gte:0',
            'content' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        $article = KbArticle::find($article_id);
        $article->category_id = $category_id;
        $article->subject = $request->input('subject');
        $article->content = $request->input('content');
        $article->order = $request->input('order');
        $article->save();

        return $this->respondJson(['success' => 'You have updated the support article successfully!']);
    }
    
    public function delete($category_id, $article_id)
    {
        $article = KbArticle::find($article_id);
        $article->delete();

        return $this->respondJson(['success' => 'You have deleted the support article successfully!']);
    }
}
