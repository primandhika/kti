<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display a listing of posts (public)
     */
    public function index(Request $request)
    {
        $query = Post::with(['category', 'user', 'tags'])
            ->where('status', 'published')
            ->latest('published_at');

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(12);

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
            'filters' => $request->only(['category', 'tag', 'search']),
        ]);
    }

    /**
     * Display the specified post (public)
     */
    public function show(Request $request, $year, $month, $day, $slug)
    {
        $post = Post::findByDateAndSlug($year, $month, $day, $slug);
        $post->load(['category', 'user', 'tags']);

        $post->incrementViewCount();

        // Reaction counts & user reactions
        $sessionId = $request->session()->getId();
        $reactionCounts = \App\Models\PostReaction::where('post_id', $post->id)
            ->selectRaw('reaction_type, COUNT(*) as total')
            ->groupBy('reaction_type')
            ->pluck('total', 'reaction_type');
        $userReactions = \App\Models\PostReaction::where('post_id', $post->id)
            ->where('session_id', $sessionId)
            ->pluck('reaction_type');

        // Get related posts (same category)
        $relatedPosts = Post::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        // Get recent posts for sidebar
        $recentPosts = Post::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(5)
            ->get(['id', 'title', 'slug', 'published_at', 'featured_image']);

        // Get archive count
        $archiveCount = Post::where('status', 'published')->count();

        // Build meta data for SEO
        $baseUrl = 'https://bppu.ikipsiliwangi.ac.id';
        $canonicalUrl = "{$baseUrl}/berita/{$year}/{$month}/{$day}/{$slug}";

        $metaDescription = $post->excerpt
            ? $post->excerpt
            : strip_tags(substr($post->content, 0, 160)) . '...';

        $ogImage = $post->featured_image
            ? "{$baseUrl}/storage/{$post->featured_image}"
            : "{$baseUrl}/storage/logo-round_ijokuning.png";

        return Inertia::render('Posts/Show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'recentPosts' => $recentPosts,
            'archiveCount' => $archiveCount,
            'reactionCounts' => $reactionCounts,
            'userReactions' => $userReactions,
        ])->withViewData([
            'metaTitle' => $post->title . ' - BPPU IKIP Siliwangi',
            'metaDescription' => $metaDescription,
            'metaKeywords' => $post->keywords ?: 'BPPU, IKIP Siliwangi, ' . ($post->category?->name ?? 'Berita'),
            'metaAuthor' => $post->user?->name ?? 'BPPU IKIP Siliwangi',
            'ogType' => 'article',
            'ogUrl' => $canonicalUrl,
            'ogTitle' => $post->title,
            'ogDescription' => $metaDescription,
            'ogImage' => $ogImage,
            'ogSiteName' => 'BPPU IKIP Siliwangi',
            'ogLocale' => 'id_ID',
            'articlePublishedTime' => $post->published_at,
            'articleModifiedTime' => $post->updated_at,
            'articleAuthor' => $post->user?->name ?? '',
            'articleSection' => $post->category?->name ?? 'Berita',
            'canonicalUrl' => $canonicalUrl,
        ]);
    }
}
