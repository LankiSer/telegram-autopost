<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        // Get unique plans - using a different approach that's compatible with MySQL's only_full_group_by mode
        $planNames = SubscriptionPlan::select('name')->distinct()->pluck('name');
        $plans = collect();
        
        foreach ($planNames as $name) {
            // Get the newest plan for each name
            $plan = SubscriptionPlan::where('name', $name)
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($plan) {
                $plans->push($plan);
            }
        }
            
        $currentSubscription = auth()->user()->activeSubscription();
        
        return Inertia::render('Subscriptions/Index', [
            'plans' => $plans,
            'currentSubscription' => $currentSubscription ? [
                'id' => $currentSubscription->id,
                'name' => $currentSubscription->plan->name,
                'description' => $currentSubscription->plan->description,
                'starts_at' => $currentSubscription->starts_at,
                'ends_at' => $currentSubscription->ends_at,
                'status' => $currentSubscription->status,
                'plan_id' => $currentSubscription->plan_id,
                'channel_limit' => $currentSubscription->plan->channel_limit,
                'posts_per_month' => $currentSubscription->plan->posts_per_month,
                'scheduling_enabled' => $currentSubscription->plan->scheduling_enabled,
                'analytics_enabled' => $currentSubscription->plan->analytics_enabled,
                'price' => $currentSubscription->plan->price,
            ] : null
        ]);
    }
    
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id'
        ]);
        
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);
        
        // В реальном проекте здесь была бы интеграция с платежной системой
        // Для демонстрации просто создаем подписку со статусом active
        
        // Отменяем текущую активную подписку
        $currentSubscription = auth()->user()->activeSubscription();
        if ($currentSubscription) {
            $currentSubscription->update([
                'status' => 'cancelled',
                'ends_at' => now()
            ]);
        }
        
        // Создаем новую подписку
        $subscription = Subscription::create([
            'user_id' => auth()->id(),
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(), // Подписка на месяц
            'status' => 'active'
        ]);
        
        return redirect()->route('subscriptions.index')
            ->with('success', "Вы успешно подписались на тариф {$plan->name}");
    }
    
    public function cancel()
    {
        $subscription = auth()->user()->activeSubscription();
        
        if ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'ends_at' => now()
            ]);
            
            return redirect()->route('subscriptions.index')
                ->with('success', 'Подписка успешно отменена');
        }
        
        return redirect()->route('subscriptions.index')
            ->with('error', 'У вас нет активной подписки');
    }
} 