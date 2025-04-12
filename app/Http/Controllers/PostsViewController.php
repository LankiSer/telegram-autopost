<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostsViewController extends Controller
{
    /**
     * Display the posts page.
     */
    public function index(Request $request)
    {
        try {
            $channels = Channel::where('user_id', auth()->id())->get();
            
            $postsQuery = Post::query()
                ->whereIn('channel_id', $channels->pluck('id'))
                ->with('channel');
            
            if ($request->has('channel') && $request->channel) {
                $postsQuery->where('channel_id', $request->channel);
            }
            
            if ($request->has('status') && $request->status) {
                $postsQuery->where('status', $request->status);
            }
            
            $posts = $postsQuery->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString();
            
            $posts->through(function($post) {
                if (!isset($post->title) || empty($post->title)) {
                    $sanitizedContent = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strip_tags($post->content ?? ''));
                    $post->title = !empty($sanitizedContent) 
                        ? substr($sanitizedContent, 0, 50) . (strlen($sanitizedContent) > 50 ? '...' : '') 
                        : 'Без названия';
                }
                return $post;
            });
            
            return Inertia::render('Posts/Index', [
                'posts' => $posts,
                'channels' => $channels,
                'filters' => $request->only(['channel', 'status'])
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading posts view', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Inertia::render('Posts/Index', [
                'posts' => [],
                'channels' => [],
                'filters' => $request->only(['channel', 'status']),
                'error' => 'Произошла ошибка при загрузке постов. Пожалуйста, попробуйте позже.'
            ]);
        }
    }
} 