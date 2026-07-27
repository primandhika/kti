<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $posts = Post::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->get();

        $pages = Page::orderBy('updated_at', 'desc')->get();

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Homepage
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . url('/') . '</loc>';
        $sitemap .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
        $sitemap .= '<changefreq>daily</changefreq>';
        $sitemap .= '<priority>1.0</priority>';
        $sitemap .= '</url>';

        // Berita index
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . route('posts.index') . '</loc>';
        $sitemap .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
        $sitemap .= '<changefreq>daily</changefreq>';
        $sitemap .= '<priority>0.9</priority>';
        $sitemap .= '</url>';

        // Posts
        foreach ($posts as $post) {
            if (!$post->published_at) continue;
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . route('posts.show', [
                'year' => $post->published_at->format('Y'),
                'month' => $post->published_at->format('m'),
                'day' => $post->published_at->format('d'),
                'slug' => $post->slug
            ]) . '</loc>';
            $sitemap .= '<lastmod>' . $post->updated_at->toAtomString() . '</lastmod>';
            $sitemap .= '<changefreq>weekly</changefreq>';
            $sitemap .= '<priority>0.8</priority>';
            $sitemap .= '</url>';
        }

        // Pages
        foreach ($pages as $page) {
            if ($page->category) {
                $loc = route('pages.show.category', [
                    'category' => $page->category,
                    'slug' => $page->slug
                ]);
            } else {
                $loc = route('pages.show', ['slug' => $page->slug]);
            }

            $sitemap .= '<url>';
            $sitemap .= '<loc>' . $loc . '</loc>';
            $sitemap .= '<lastmod>' . $page->updated_at->toAtomString() . '</lastmod>';
            $sitemap .= '<changefreq>monthly</changefreq>';
            $sitemap .= '<priority>0.7</priority>';
            $sitemap .= '</url>';
        }

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'text/xml');
    }
}
