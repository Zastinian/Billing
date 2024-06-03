<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Plan;
use App\Models\PlanCycle;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanController extends ApiController
{
    private $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'order' => 'required|numeric',
        'category' => 'required|integer|gte:1',
        'ram' => 'required|integer|gte:0',
        'cpu' => 'required|integer|gte:0',
        'disk' => 'required|integer|gte:0',
        'swap' => 'required|integer|gte:-1',
        'io' => 'required|integer|gte:10|lte:1000',
        'databases' => 'required|integer|gte:0',
        'backups' => 'required|integer|gte:0',
        'extra_ports' => 'required|integer|gte:0',
        'node' => 'required|array',
        'min_port' => 'nullable|integer|gte:1',
        'max_port' => 'nullable|integer|lte:65535',
        'egg' => 'required|array',
        'server_description' => 'nullable|string',
        'discount' => 'nullable|integer|gte:1',
        'coupon' => 'nullable|array',
        'days_before_suspend' => 'required|integer|gte:0',
        'days_before_delete' => 'required|integer|gte:0',
        'global_limit' => 'nullable|integer|gte:0',
        'per_client_limit' => 'nullable|integer|gte:0',
        'per_client_trial_limit' => 'nullable|integer|gte:0',
        'cycle' => 'required|array',
        'cycle.*.cycle_length' => 'required|integer|gte:1',
        'cycle.*.cycle_type' => 'required|integer|in:0,1,2,3,4',
        'cycle.*.init_price' => 'required|numeric|gte:0',
        'cycle.*.renew_price' => 'required|numeric|gte:0',
        'cycle.*.setup_fee' => 'required|numeric|gte:0',
        'cycle.*.late_fee' => 'required|numeric|gte:0',
        'cycle.*.trial_length' => 'nullable|integer|gte:0',
        'cycle.*.trial_type' => 'nullable|integer|in:1,2,3,4',
    ];

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        $plan = Plan::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'category_id' => $request->input('category'),
            'ram' => $request->input('ram'),
            'cpu' => $request->input('cpu'),
            'disk' => $request->input('disk'),
            'swap' => $request->input('swap'),
            'io' => $request->input('io'),
            'databases' => $request->input('databases'),
            'backups' => $request->input('backups'),
            'extra_ports' => $request->input('extra_ports'),
            'locations_nodes_id' => json_encode($request->input('node')),
            'min_port' => $request->input('min_port'),
            'max_port' => $request->input('max_port'),
            'nests_eggs_id' => json_encode($request->input('egg')),
            'server_description' => $request->input('server_description'),
            'discount' => $request->input('discount'),
            'coupons' => json_encode($request->input('coupon', [])),
            'days_before_suspend' => $request->input('days_before_suspend'),
            'days_before_delete' => $request->input('days_before_delete'),
            'global_limit' => $request->input('global_limit'),
            'per_client_limit' => $request->input('per_client_limit'),
            'per_client_trial_limit' => $request->input('per_client_trial_limit'),
            'order' => $request->input('order'),
        ]);

        foreach ($request->input('cycle') as $cycle) {
            PlanCycle::create([
                'plan_id' => $plan->id,
                'cycle_length' => $cycle['cycle_length'],
                'cycle_type' => $cycle['cycle_type'],
                'init_price' => $cycle['init_price'],
                'renew_price' => $cycle['renew_price'],
                'setup_fee' => $cycle['setup_fee'],
                'late_fee' => $cycle['late_fee'],
                'trial_length' => $cycle['trial_length'],
                'trial_type' => $cycle['trial_type'],
            ]);
        }
        
        return $this->respondJson(['success' => 'You have created a server plan successfully!']);
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        $plan = Plan::find($id);
        $plan->name = $request->input('name');
        $plan->description = $request->input('description');
        $plan->category_id = $request->input('category');
        $plan->ram = $request->input('ram');
        $plan->cpu = $request->input('cpu');
        $plan->disk = $request->input('disk');
        $plan->swap = $request->input('swap');
        $plan->io = $request->input('io');
        $plan->databases = $request->input('databases');
        $plan->backups = $request->input('backups');
        $plan->extra_ports = $request->input('extra_ports');
        $plan->locations_nodes_id = json_encode($request->input('node'));
        $plan->min_port = $request->input('min_port');
        $plan->max_port = $request->input('max_port');
        $plan->nests_eggs_id = json_encode($request->input('egg'));
        $plan->server_description = $request->input('server_description');
        $plan->discount = $request->input('discount');
        $plan->coupons = json_encode($request->input('coupon', []));
        $plan->days_before_suspend = $request->input('days_before_suspend');
        $plan->days_before_delete = $request->input('days_before_delete');
        $plan->global_limit = $request->input('global_limit');
        $plan->per_client_limit = $request->input('per_client_limit');
        $plan->per_client_trial_limit = $request->input('per_client_trial_limit');
        $plan->order = $request->input('order');
        $plan->save();

        $plan_cycles = PlanCycle::where('plan_id', $id)->get();
        $update_failed = $delete_failed = false;
        foreach ($request->input('cycle') as $cycle) {
            foreach ($plan_cycles as $plan_cycle) {
                if ($cycle['cycle_length'] == $plan_cycle->cycle_length && $cycle['cycle_type'] == $plan_cycle->cycle_type) {
                    $plan_cycle->init_price = $cycle['init_price'];
                    $plan_cycle->renew_price = $cycle['renew_price'];
                    $plan_cycle->setup_fee = $cycle['setup_fee'];
                    $plan_cycle->late_fee = $cycle['late_fee'];
                    $plan_cycle->trial_length = $cycle['trial_length'];
                    $plan_cycle->trial_type = $cycle['trial_type'];

                    if ($plan_cycle->isDirty())
                        if (Server::where('plan_cycle', $plan_cycle->id)->doesntExist()) {
                            $plan_cycle->save();
                        } else {
                        }
                    
                    continue 2;
                }
            }
            
            PlanCycle::create([
                'plan_id' => $plan->id,
                'cycle_length' => $cycle['cycle_length'],
                'cycle_type' => $cycle['cycle_type'],
                'init_price' => $cycle['init_price'],
                'renew_price' => $cycle['renew_price'],
                'setup_fee' => $cycle['setup_fee'],
                'late_fee' => $cycle['late_fee'],
                'trial_length' => $cycle['trial_length'],
                'trial_type' => $cycle['trial_type'],
            ]);
        }

        foreach ($plan_cycles as $plan_cycle) {
            foreach ($request->input('cycle') as $cycle)
                if ($cycle['cycle_length'] == $plan_cycle->cycle_length && $cycle['cycle_type'] == $plan_cycle->cycle_type) {
                    continue 2;
                }
                
            if (Server::where('plan_cycle', $plan_cycle->id)->doesntExist()) {
                $plan_cycle->delete();
            } else {
                $delete_failed = true;
            }
        }

        return $this->respondJson(['success' => 'You have updated the server plan successfully!', 'update_failed' => $update_failed, 'delete_failed' => $delete_failed]);
    }
    
    public function delete($id)
    {
        if (Server::where('plan_id', $id)->exists()) {
            return $this->respondJson(['error' => 'You cannot delete this server plan because some servers are still using it!']);
        }
        
        $plan = Plan::find($id);
        $plan->delete();

        return $this->respondJson(['success' => 'You have deleted the server plan successfully!']);
    }
}
