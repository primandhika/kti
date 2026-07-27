<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Models\Arsip;
use App\Models\ArsipUsage;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::with(['category', 'user', 'tags']);

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('keywords', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        // Validate sort fields
        $allowedSorts = ['title', 'status', 'created_at', 'published_at'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'created_at';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query->orderBy($sortField, $sortDirection);

        $posts = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Posts/Index', [
            'posts' => $posts,
            'filters' => $request->only(['search', 'status', 'category', 'sort', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::select('id', 'name', 'username')
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['officer', 'sysadmin']);
            })
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Posts/CreateEdit', [
            'categories' => Category::all(),
            'tags' => Tag::all(),
            'users' => $users,
        ]);
    }

    private function validationMessages(): array
    {
        return [
            'title.required'   => 'Judul pos wajib diisi.',
            'title.max'        => 'Judul pos maksimal 255 karakter.',
            'slug.unique'      => 'Slug sudah digunakan, gunakan slug lain.',
            'content.required' => 'Konten pos wajib diisi.',
            'featured_image.image' => 'File gambar tidak valid.',
            'featured_image.max'   => 'Ukuran gambar maksimal 10MB.',
            'status.required'  => 'Status pos wajib dipilih.',
            'status.in'        => 'Status hanya boleh draft atau published.',
            'published_at.date' => 'Format tanggal publikasi tidak valid.',
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ImageCompressionService $imageCompressionService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:posts,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:10240',
            'arsip_id' => 'nullable|exists:arsip,id',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'keywords' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'user_id' => 'nullable|exists:users,id',
        ], $this->validationMessages());

        // Handle featured image from upload or arsip
        if ($request->has('arsip_id') && $request->arsip_id) {
            $arsip = Arsip::find($request->arsip_id);
            if ($arsip) {
                $validated['featured_image'] = $arsip->path;
            }
        } elseif ($request->hasFile('featured_image')) {
            try {
                $validated['featured_image'] = $imageCompressionService->compressAndStore(
                    $request->file('featured_image'),
                    'posts',
                    1200,
                    85,
                    300
                );
            } catch (\Throwable $e) {
                $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
            }
        }

        if (!isset($validated['user_id'])) {
            $validated['user_id'] = auth()->id();
        }

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post = Post::create($validated);

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        // Track arsip usage if using image from arsip
        if ($request->has('arsip_id') && $request->arsip_id) {
            ArsipUsage::create([
                'arsip_id' => $request->arsip_id,
                'usable_type' => Post::class,
                'usable_id' => $post->id,
                'field_name' => 'featured_image',
            ]);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $po)
    {
        return Inertia::render('Admin/Posts/Show', [
            'post' => $po->load(['category', 'user', 'tags']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $po)
    {
        $users = User::select('id', 'name', 'username')
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['officer', 'sysadmin']);
            })
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Posts/CreateEdit', [
            'post' => $po->load(['tags', 'category']),
            'categories' => Category::all(),
            'tags' => Tag::all(),
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $po, ImageCompressionService $imageCompressionService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:posts,slug,' . $po->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:10240',
            'arsip_id' => 'nullable|exists:arsip,id',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'keywords' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'user_id' => 'nullable|exists:users,id',
        ], $this->validationMessages());

        // Handle featured image from upload or arsip
        if ($request->has('arsip_id') && $request->arsip_id) {
            // Remove old usage tracking
            ArsipUsage::where('usable_type', Post::class)
                ->where('usable_id', $po->id)
                ->where('field_name', 'featured_image')
                ->delete();

            $arsip = Arsip::find($request->arsip_id);
            if ($arsip) {
                // Delete old uploaded image if not from arsip
                if ($po->featured_image && !str_starts_with($po->featured_image, 'arsip/')) {
                    Storage::disk('public')->delete($po->featured_image);
                }
                $validated['featured_image'] = $arsip->path;

                // Create new usage tracking
                ArsipUsage::create([
                    'arsip_id' => $request->arsip_id,
                    'usable_type' => Post::class,
                    'usable_id' => $po->id,
                    'field_name' => 'featured_image',
                ]);
            }
        } elseif ($request->hasFile('featured_image')) {
            // Remove old usage tracking
            ArsipUsage::where('usable_type', Post::class)
                ->where('usable_id', $po->id)
                ->where('field_name', 'featured_image')
                ->delete();

            if ($po->featured_image) {
                Storage::disk('public')->delete($po->featured_image);
            }
            try {
                $validated['featured_image'] = $imageCompressionService->compressAndStore(
                    $request->file('featured_image'),
                    'posts',
                    1200,
                    85,
                    300
                );
            } catch (\Throwable $e) {
                $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
            }
        }

        if ($validated['status'] === 'published' && empty($po->published_at) && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $po->update($validated);

        if ($request->has('tags')) {
            $po->tags()->sync($request->tags);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $po)
    {
        if ($po->featured_image) {
            Storage::disk('public')->delete($po->featured_image);
        }

        $po->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post berhasil dihapus!');
    }

    /**
     * Upload image for post content
     */
    public function uploadContentImage(Request $request, ImageCompressionService $imageCompressionService)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        try {
            $path = $imageCompressionService->compressAndStore(
                $request->file('image'),
                'posts/content',
                1600,
                90,
                260
            );
        } catch (\Throwable $e) {
            $path = $request->file('image')->store('posts/content', 'public');
        }

        $url = Storage::url($path);

        return back()->with('imageUrl', $url);
    }
}
