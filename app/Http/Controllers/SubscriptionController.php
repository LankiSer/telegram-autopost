<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::all();
        $currentSubscription = auth()->user()->activeSubscription();
        
        return view('subscriptions.index', compact('plans', 'currentSubscription'));
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
        
        return redirect()->route('dashboard')
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
            
            return back()->with('success', 'Подписка успешно отменена');
        }
        
        return back()->with('error', 'У вас нет активной подписки');
    }
} 