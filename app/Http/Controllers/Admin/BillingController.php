<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\Tax;
use Carbon\Carbon;

class BillingController extends Controller
{
    public function income()
    {
        $incomes7d = Income::whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()])->get();
        $incomes30d = Income::whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()])->get();
        $incomes90d = Income::whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->get();
        $incomesYear = Income::whereBetween('created_at', [Carbon::now()->subYear(), Carbon::now()])->get();

        $total_income7d = $total_income30d = $total_income90d = $total_incomeYear = 0;

        foreach ($incomes7d as $income) {
            $total_income7d += $income->price;
        }
        
        foreach ($incomes30d as $income) {
            $total_income30d += $income->price;
        }
        
        foreach ($incomes90d as $income) {
            $total_income90d += $income->price;
        }
        
        foreach ($incomesYear as $income) {
            $total_incomeYear += $income->price;
        }
        return view('admin.income', ['incomes' => [$incomes7d, $incomes30d, $incomes90d, $incomesYear], 'total_income' => [$total_income7d, $total_income30d, $total_income90d, $total_incomeYear]]);
    }
    
    public function invoices()
    {
        return view('admin.invoice.index');
    }
    
    public function invoice($id)
    {
        return view('admin.invoice.show', ['id' => $id, 'invoice' => Invoice::find($id)]);
    }
    
    public function currencies()
    {
        return view('admin.currency.index');
    }
    
    public function createCurrency()
    {
        return view('admin.currency.create');
    }
    
    public function currency($id)
    {
        return view('admin.currency.show', ['id' => $id, 'currency' => Currency::find($id)]);
    }
    
    public function taxes()
    {
        return view('admin.tax.index');
    }
    
    public function createTax()
    {
        return view('admin.tax.create');
    }
    
    public function tax($id)
    {
        return view('admin.tax.show', ['id' => $id, 'tax' => Tax::find($id)]);
    }
}
