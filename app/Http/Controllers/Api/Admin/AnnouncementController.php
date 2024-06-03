<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends ApiController
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enabled' => 'required|boolean',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'theme' => 'required|integer|in:0,1,2,3',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        Announcement::create([
            'enabled' => $request->input('enabled') === '1',
            'subject' => $request->input('subject'),
            'content' => $request->input('content'),
            'theme' => $request->input('theme'),
        ]);
        return $this->respondJson(['success' => 'You have created an announcement successfully! Click Reload Config to see the changes.']);
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'enabled' => 'required|boolean',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'theme' => 'required|integer|in:0,1,2,3',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        $announcement = Announcement::find($id);
        $announcement->enabled = $request->input('enabled') === '1';
        $announcement->subject = $request->input('subject');
        $announcement->content = $request->input('content');
        $announcement->theme = $request->input('theme');
        $announcement->save();
        
        return $this->respondJson(['success' => 'You have updated the announcement successfully! Click Reload Config to see the changes.']);
    }
    
    public function delete($id)
    {
        $announcement = Announcement::find($id);
        $announcement->delete();
        return $this->respondJson(['success' => 'You have deleted the announcement successfully! Click Reload Config to see the changes.']);
    }
}
