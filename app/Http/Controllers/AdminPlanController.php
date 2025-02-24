<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class AdminPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->get();
        return view('admin.plans.index', compact('plans'));
    }
    
    public function create()
    {
        return view('admin.plans.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'channel_limit' => 'required|integer|min:1',
            'posts_per_month' => 'required|integer|min:1',
            'scheduling_enabled' => 'boolean',
            'analytics_enabled' => 'boolean',
        ]);
        
        SubscriptionPlan::create($validated);
        
        return redirect()->route('admin.plans')
            ->with('success', 'Тарифный план успешно создан');
    }
    
    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }
    
    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'channel_limit' => 'required|integer|min:1',
            'posts_per_month' => 'required|integer|min:1',
            'scheduling_enabled' => 'boolean',
            'analytics_enabled' => 'boolean',
        ]);
        
        $plan->update($validated);
        
        return redirect()->route('admin.plans')
            ->with('success', 'Тарифный план успешно обновлен');
    }
    
    public function destroy(SubscriptionPlan $plan)
    {
        // Проверка, что у плана нет активных подписок
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            return back()->with('error', 'Нельзя удалить план с активными подписками');
        }
        
        $plan->delete();
        
        return redirect()->route('admin.plans')
            ->with('success', 'Тарифный план успешно удален');
    }
} 