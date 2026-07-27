<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\User;
use App\Models\Arsip;
use App\Models\ArsipUsage;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Page::with('user')->latest();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $pages = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Pages/Index', [
            'pages' => $pages,
            'filters' => $request->only(['search', 'status']),
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

        return Inertia::render('Admin/Pages/CreateEdit', [
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    private function validationMessages(): array
    {
        return [
            'title.required'   => 'Judul halaman wajib diisi.',
            'title.max'        => 'Judul halaman maksimal 255 karakter.',
            'slug.unique'      => 'Slug sudah digunakan, gunakan slug lain.',
            'content.required' => 'Konten halaman wajib diisi.',
            'featured_image.image' => 'File gambar tidak valid.',
            'featured_image.max'   => 'Ukuran gambar maksimal 2MB.',
            'status.required'  => 'Status halaman wajib dipilih.',
            'status.in'        => 'Status hanya boleh draft atau published.',
        ];
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|unique:pages,slug',
                'category' => 'nullable|string|max:100',
                'content' => 'required|string',
                'featured_image' => 'nullable|image|max:2048',
                'arsip_id' => 'nullable|exists:arsip,id',
                'status' => 'required|in:draft,published',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'user_id' => 'nullable|exists:users,id',
            ], $this->validationMessages());

            // Handle featured image from upload or arsip
            if ($request->has('arsip_id') && $request->arsip_id) {
                $arsip = Arsip::find($request->arsip_id);
                if ($arsip) {
                    $validated['featured_image'] = $arsip->path;
                }
            } elseif ($request->hasFile('featured_image')) {
                $validated['featured_image'] = $request->file('featured_image')
                    ->store('pages', 'public');
            }

            // If user_id not provided, use current auth user
            if (!isset($validated['user_id'])) {
                $validated['user_id'] = auth()->id();
            }

            $page = Page::create($validated);

            // Track arsip usage if using image from arsip
            if ($request->has('arsip_id') && $request->arsip_id) {
                ArsipUsage::create([
                    'arsip_id' => $request->arsip_id,
                    'usable_type' => Page::class,
                    'usable_id' => $page->id,
                    'field_name' => 'featured_image',
                ]);
            }

            return redirect()->route('admin.pages.index')
                ->with('success', 'Halaman berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->route('admin.pages.index')
                ->with('error', 'Gagal membuat halaman: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $halaman)
    {
        return Inertia::render('Admin/Pages/Show', [
            'page' => $halaman->load('user'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $halaman)
    {
        $users = User::select('id', 'name', 'username')
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['officer', 'sysadmin']);
            })
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Pages/CreateEdit', [
            'page' => $halaman,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $halaman)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|unique:pages,slug,' . $halaman->id,
                'category' => 'nullable|string|max:100',
                'content' => 'required|string',
                'featured_image' => 'nullable|image|max:2048',
                'arsip_id' => 'nullable|exists:arsip,id',
                'status' => 'required|in:draft,published',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'user_id' => 'nullable|exists:users,id',
            ], $this->validationMessages());

            // Handle featured image from upload or arsip
            if ($request->has('arsip_id') && $request->arsip_id) {
                // Remove old usage tracking
                ArsipUsage::where('usable_type', Page::class)
                    ->where('usable_id', $halaman->id)
                    ->where('field_name', 'featured_image')
                    ->delete();

                $arsip = Arsip::find($request->arsip_id);
                if ($arsip) {
                    // Delete old uploaded image if not from arsip
                    if ($halaman->featured_image && !str_starts_with($halaman->featured_image, 'arsip/')) {
                        Storage::disk('public')->delete($halaman->featured_image);
                    }
                    $validated['featured_image'] = $arsip->path;

                    // Create new usage tracking
                    ArsipUsage::create([
                        'arsip_id' => $request->arsip_id,
                        'usable_type' => Page::class,
                        'usable_id' => $halaman->id,
                        'field_name' => 'featured_image',
                    ]);
                }
            } elseif ($request->hasFile('featured_image')) {
                // Remove old usage tracking
                ArsipUsage::where('usable_type', Page::class)
                    ->where('usable_id', $halaman->id)
                    ->where('field_name', 'featured_image')
                    ->delete();

                if ($halaman->featured_image) {
                    Storage::disk('public')->delete($halaman->featured_image);
                }
                $validated['featured_image'] = $request->file('featured_image')
                    ->store('pages', 'public');
            }

            $halaman->update($validated);

            return redirect()->route('admin.pages.index')
                ->with('success', 'Halaman berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('admin.pages.index')
                ->with('error', 'Gagal memperbarui halaman: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $halaman)
    {
        try {
            if ($halaman->featured_image) {
                Storage::disk('public')->delete($halaman->featured_image);
            }

            $halaman->delete();

            return redirect()->route('admin.pages.index')
                ->with('success', 'Halaman berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin.pages.index')
                ->with('error', 'Gagal menghapus halaman: ' . $e->getMessage());
        }
    }

    /**
     * Upload image for page content
     */
    public function uploadContentImage(Request $request, ImageCompressionService $imageCompressionService)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        try {
            $path = $imageCompressionService->compressAndStore(
                $request->file('image'),
                'pages/content',
                1600,
                90,
                260
            );
        } catch (\Throwable $e) {
            $path = $request->file('image')->store('pages/content', 'public');
        }

        $url = Storage::url($path);

        return back()->with('imageUrl', $url);
    }
}
