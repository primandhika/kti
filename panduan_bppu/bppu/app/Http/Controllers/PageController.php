<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Display the specified page (public)
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->with('user:id,name')
            ->firstOrFail();

        return $this->renderPageWithMeta($page, url("/{$slug}"));
    }

    /**
     * Display the specified page by category (public)
     */
    public function showByCategory($category, $slug)
    {
        $page = Page::where('category', $category)
            ->where('slug', $slug)
            ->with('user:id,name')
            ->firstOrFail();

        return $this->renderPageWithMeta($page, url("/{$category}/{$slug}"));
    }

    /**
     * Render page with SEO meta tags
     */
    private function renderPageWithMeta($page, $canonicalUrl)
    {
        $baseUrl = 'https://bppu.ikipsiliwangi.ac.id';

        $metaDescription = $page->meta_description
            ?: strip_tags(substr($page->content, 0, 160)) . '...';

        $ogImage = $page->featured_image
            ? "{$baseUrl}/storage/{$page->featured_image}"
            : "{$baseUrl}/storage/logo-round_ijokuning.png";

        return Inertia::render('Pages/Show', [
            'page' => $page,
        ])->withViewData([
            'metaTitle' => ($page->meta_title ?: $page->title) . ' - BPPU IKIP Siliwangi',
            'metaDescription' => $metaDescription,
            'metaKeywords' => $page->meta_keywords ?: 'BPPU, IKIP Siliwangi, ' . ($page->category ?? 'informasi'),
            'metaAuthor' => $page->user?->name ?? 'BPPU IKIP Siliwangi',
            'ogType' => 'article',
            'ogUrl' => $canonicalUrl,
            'ogTitle' => $page->meta_title ?: $page->title,
            'ogDescription' => $metaDescription,
            'ogImage' => $ogImage,
            'ogSiteName' => 'BPPU IKIP Siliwangi',
            'ogLocale' => 'id_ID',
            'articlePublishedTime' => $page->created_at,
            'articleModifiedTime' => $page->updated_at,
            'articleAuthor' => $page->user?->name ?? '',
            'canonicalUrl' => $canonicalUrl,
        ]);
    }
}
