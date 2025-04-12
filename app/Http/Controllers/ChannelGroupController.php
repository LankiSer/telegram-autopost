<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\ChannelGroup;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ChannelGroupController extends Controller
{
    /**
     * Display a listing of the channel groups.
     */
    public function index()
    {
        $groups = Auth::user()->channelGroups()
            ->withCount('channels')
            ->get();
        
        return Inertia::render('ChannelGroups/Index', [
            'groups' => $groups
        ]);
    }

    /**
     * Show the form for creating a new channel group.
     */
    public function create()
    {
        $channels = Auth::user()->channels()->get();
        return Inertia::render('ChannelGroups/Create', [
            'channels' => $channels
        ]);
    }

    /**
     * Store a newly created channel group in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'channels' => 'array',
            'channels.*' => 'exists:channels,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $group = new ChannelGroup();
        $group->user_id = Auth::id();
        $group->name = $request->name;
        $group->description = $request->description;
        $group->category = $request->category;
        $group->save();

        // Attach channels if any selected
        if ($request->has('channels') && !empty($request->channels)) {
            $group->channels()->attach($request->channels);
        }

        return redirect()->route('channel-groups.index')
            ->with('success', 'Группа каналов успешно создана');
    }

    /**
     * Display the specified channel group.
     */
    public function show(ChannelGroup $channelGroup)
    {
        if (Auth::id() !== $channelGroup->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $channelGroup->load('channels');

        // Подготовка статистики для отображения
        $statistics = [
            'channels_count' => $channelGroup->channels->count(),
            'total_members' => $channelGroup->channels->sum('members_count'),
            'total_reach' => 0 // Можно добавить реальный расчет
        ];
        
        return Inertia::render('ChannelGroups/Show', [
            'group' => $channelGroup,
            'channels' => $channelGroup->channels,
            'statistics' => $statistics
        ]);
    }

    /**
     * Show the form for editing the specified channel group.
     */
    public function edit(ChannelGroup $channelGroup)
    {
        if (Auth::id() !== $channelGroup->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $channels = Auth::user()->channels()->get();
        $selectedChannels = $channelGroup->channels->pluck('id')->toArray();
        
        return Inertia::render('ChannelGroups/Edit', [
            'group' => $channelGroup,
            'channels' => $channels,
            'selectedChannels' => $selectedChannels
        ]);
    }

    /**
     * Update the specified channel group in storage.
     */
    public function update(Request $request, ChannelGroup $channelGroup)
    {
        if (Auth::id() !== $channelGroup->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'channels' => 'array',
            'channels.*' => 'exists:channels,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $channelGroup->name = $request->name;
        $channelGroup->description = $request->description;
        $channelGroup->category = $request->category;
        $channelGroup->save();

        // Sync channels
        $channelGroup->channels()->sync($request->channels ?? []);

        return redirect()->route('channel-groups.index')
            ->with('success', 'Группа каналов успешно обновлена');
    }

    /**
     * Remove the specified channel group from storage.
     */
    public function destroy(ChannelGroup $channelGroup)
    {
        if (Auth::id() !== $channelGroup->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $channelGroup->channels()->detach();
        $channelGroup->delete();

        return redirect()->route('channel-groups.index')
            ->with('success', 'Группа каналов успешно удалена');
    }
    
    /**
     * Generate cross-promotion posts between channels in the group
     */
    public function generateCrossPromotion(ChannelGroup $channelGroup, Request $request)
    {
        if (Auth::id() !== $channelGroup->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Ensure the group has at least 2 channels
        if ($channelGroup->channels->count() < 2) {
            return redirect()->route('channel-groups.show', $channelGroup)
                ->with('error', 'В группе должно быть минимум 2 канала для создания кросс-промо');
        }
        
        // Get all channels in the group
        $channels = $channelGroup->channels;
        
        // Track created posts
        $createdPosts = 0;
        
        // Generate cross-promotions for each channel pair
        foreach ($channels as $channel) {
            foreach ($channels as $promotedChannel) {
                // Skip self-promotion
                if ($channel->id === $promotedChannel->id) {
                    continue;
                }
                
                // Create a new post for cross-promotion
                $post = new Post();
                $post->channel_id = $channel->id;
                $post->user_id = Auth::id();
                $post->title = "Кросс-промо для: {$promotedChannel->name}";
                $post->content = "Рекомендуем подписаться на канал {$promotedChannel->name}!\n\n";
                
                if ($promotedChannel->description) {
                    $post->content .= "О канале: {$promotedChannel->description}\n\n";
                }
                
                // Add telegram link if available
                if ($promotedChannel->telegram_username) {
                    $post->content .= "Ссылка: https://t.me/{$promotedChannel->telegram_username}";
                }
                
                $post->status = 'draft';
                $post->is_cross_promotion = true;
                $post->promoted_from_channel_id = $promotedChannel->id;
                $post->save();
                
                $createdPosts++;
            }
        }
        
        return redirect()->route('channel-groups.show', $channelGroup)
            ->with('success', "Успешно создано $createdPosts кросс-промо постов! Вы можете найти их в разделе Черновики каждого канала.");
    }

    public function debug()
    {
        try {
            $groups = Auth::user()->channelGroups()
                ->withCount('channels')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'groups' => $groups
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
