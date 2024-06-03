<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends ApiController
{
    public function store(Request $request)
    {
        $required = 'required|string|max:255';
        $required_boolean = 'required|boolean';
        $optional = 'nullable|string|max:255';

        $validator = Validator::make($request->all(), [
            'company_name' => $required,
            'store_url' => $required,
            'logo_path' => $required,
            'favicon_path' => $required,
            'dark_mode' => $required_boolean,
            'open_registration' => $required_boolean,
            'panel_url' => $required,
            'panel_client_api_key' => $required,
            'panel_app_api_key' => $required,
            'phpmyadmin_url' => $optional,
            'hcaptcha_site_key' => $optional,
            'hcaptcha_secret_key' => $optional,
            'google_analytics_id' => $optional,
            'arc_widget_id' => $optional,
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        $this->saveSetting($key = 'company_name', $request->input($key));
        $this->saveSetting($key = 'store_url', $request->input($key));
        $this->saveSetting($key = 'logo_path', $request->input($key));
        $this->saveSetting($key = 'favicon_path', $request->input($key));
        $this->saveSetting($key = 'dark_mode', $request->input($key) === '1');
        $this->saveSetting($key = 'open_registration', $request->input($key) === '1');
        $this->saveSetting($key = 'panel_url', $request->input($key));
        $this->saveSetting($key = 'panel_client_api_key', $request->input($key));
        $this->saveSetting($key = 'panel_app_api_key', $request->input($key));
        $this->saveSetting($key = 'phpmyadmin_url', $request->input($key));
        $this->saveSetting($key = 'hcaptcha_site_key', $request->input($key));
        $this->saveSetting($key = 'hcaptcha_secret_key', $request->input($key));
        $this->saveSetting($key = 'google_analytics_id', $request->input($key));
        $this->saveSetting($key = 'arc_widget_id', $request->input($key));

        return $this->respondJson(['success' => 'You have updated the store settings successfully! Reloading configurations...']);
    }

    private function saveSetting($key, $value)
    {
        $setting = Setting::where('key', $key)->first();
        $setting->value = $value;
        $setting->save();
    }

    public function page(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|exists:pages',
            'content' => 'present',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        $page = Page::where('name', $request->input('name'))->first();
        $page->content = $request->input('content');
        $page->save();

        return $this->respondJson(['success' => 'You have updated the page successfully! Please click \'Reload Config\' above on the navigation bar to publish the changes.']);
    }
}
