<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;

class SettingController extends Controller
{
    public function store()
    {
        return view('admin.setting.show');
    }

    public function page($name)
    {
        if (!Page::where('name', $name)->count()) return abort(404);
        return view('admin.setting.page', ['name' => $name]);
    }

    public function contact()
    {
        return view('admin.setting.contact');
    }

    public function message($msg_id)
    {
        return view('admin.setting.message', ['msg_id' => $msg_id]);
    }
}
