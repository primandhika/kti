<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Arsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ArsipController extends Controller
{
    /**
     * Display a listing of arsip
     */
    public function index(Request $request)
    {
        $query = Arsip::with('uploader:id,name')
            ->withCount('usages');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_file', 'like', "%{$search}%")
                    ->orWhere('caption', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        if ($kategoriArsip = $request->get('kategori_arsip')) {
            $query->where('kategori_arsip', $kategoriArsip);
        }

        if ($uploadedBy = $request->get('uploaded_by')) {
            $query->where('uploaded_by', $uploadedBy);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 20);
        $arsip = $query->paginate($perPage)->through(function ($item) {
            return [
                'id' => $item->id,
                'nama_file' => $item->nama_file,
                'file_name' => $item->file_name,
                'url' => $item->url,
                'mime_type' => $item->mime_type,
                'jenis' => $item->jenis,
                'kategori_arsip' => $item->kategori_arsip,
                'folder' => $item->folder,
                'tags' => $item->tags,
                'deskripsi' => $item->deskripsi,
                'ukuran' => $item->ukuran,
                'ukuran_readable' => $item->ukuran_readable,
                'original_dimensions' => $item->original_dimensions,
                'is_public' => $item->is_public,
                'alt_text' => $item->alt_text,
                'caption' => $item->caption,
                'uploaded_by' => $item->uploader ? $item->uploader->name : null,
                'usages_count' => $item->usages_count,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        // If it's an AJAX request (from MediaPicker or InsertImageModal), return JSON
        // But NOT if it's an Inertia request (Inertia also sets X-Requested-With header)
        if (($request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia')) {
            return response()->json([
                'arsip' => $arsip,
            ]);
        }

        $stats = [
            'total' => Arsip::count(),
            'images' => Arsip::where('jenis', 'image')->count(),
            'documents' => Arsip::whereIn('jenis', ['document', 'pdf'])->count(),
            'total_size' => Arsip::sum('ukuran'),
        ];

        return Inertia::render('Admin/Dashboard/Arsip', [
            'arsip' => $arsip,
            'stats' => $stats,
            'filters' => $request->only(['search', 'kategori_arsip', 'uploaded_by', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Store a newly uploaded file
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'kategori_arsip' => 'required|string',
            'folder' => 'nullable|string|max:255',
            'tags' => 'nullable|json',
            'deskripsi' => 'nullable|string',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:1000',
            'is_public' => 'nullable|boolean',
        ]);

        // Kategori yang tidak boleh publik (data sensitif)
        $privateCategories = ['dokumen_resmi', 'dokumen_laporan', 'dokumen_sk', 'dokumen_surat', 'gambar_produk', 'template', 'lainnya'];
        $isPublic = $request->is_public ?? false;

        // Force is_public = false untuk kategori sensitif
        if (in_array($request->kategori_arsip, $privateCategories)) {
            $isPublic = false;
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME))
            . '-' . time()
            . '.' . $extension;

        $jenis = $this->determineJenis($mimeType);

        $folder = $request->folder ? $request->folder : $jenis;
        $path = $file->storeAs('arsip/' . $folder, $fileName, 'public');

        $originalDimensions = null;
        if (str_starts_with($mimeType, 'image/')) {
            try {
                $imageSize = getimagesize($file->getRealPath());
                if ($imageSize) {
                    $originalDimensions = $imageSize[0] . 'x' . $imageSize[1];
                }
            } catch (\Exception $e) {
                // Ignore
            }
        }

        $tags = $request->tags ? json_decode($request->tags, true) : [];

        $arsip = Arsip::create([
            'nama_file' => $originalName,
            'file_name' => $fileName,
            'path' => $path,
            'mime_type' => $mimeType,
            'jenis' => $jenis,
            'kategori_arsip' => $request->kategori_arsip,
            'folder' => $request->folder,
            'tags' => $tags,
            'deskripsi' => $request->deskripsi,
            'ukuran' => $size,
            'original_dimensions' => $originalDimensions,
            'is_public' => $isPublic,
            'alt_text' => $request->alt_text,
            'caption' => $request->caption,
            'uploaded_by' => auth()->id(),
        ]);

        $responseData = [
            'message' => 'File berhasil diupload',
            'data' => [
                'id' => $arsip->id,
                'nama_file' => $arsip->nama_file,
                'url' => $arsip->url,
                'jenis' => $arsip->jenis,
                'kategori_arsip' => $arsip->kategori_arsip,
                'ukuran_readable' => $arsip->ukuran_readable,
            ],
        ];

        // Kalau dari MediaPicker/AJAX (bukan Inertia), return JSON
        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json($responseData);
        }

        return redirect()->back()->with('success', 'File berhasil diupload')->with('uploadedArsip', $responseData['data']);
    }

    /**
     * Update arsip metadata
     */
    public function update(Request $request, Arsip $arsip)
    {
        $validated = $request->validate([
            'nama_file' => 'required|string|max:255',
            'kategori_arsip' => 'required|string',
            'folder' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'deskripsi' => 'nullable|string',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:1000',
            'is_public' => 'nullable|boolean',
        ]);

        // Kategori yang tidak boleh publik (data sensitif)
        $privateCategories = ['dokumen_resmi', 'dokumen_laporan', 'dokumen_sk', 'dokumen_surat', 'gambar_produk', 'template', 'lainnya'];

        // Force is_public = false untuk kategori sensitif
        if (in_array($validated['kategori_arsip'], $privateCategories)) {
            $validated['is_public'] = false;
        }

        $arsip->update($validated);

        return back()->with('success', 'Arsip berhasil diupdate');
    }

    /**
     * Delete arsip
     */
    public function destroy(Arsip $arsip)
    {
        if ($arsip->usages_count > 0) {
            return response()->json([
                'message' => 'File tidak dapat dihapus karena masih digunakan',
            ], 422);
        }

        Storage::disk('public')->delete($arsip->path);
        $arsip->delete();

        return response()->json([
            'message' => 'File berhasil dihapus',
        ]);
    }

    /**
     * Get arsip usage details
     */
    public function usage(Arsip $arsip)
    {
        $typeMap = [
            'Post'        => ['label' => 'Postingan/Berita', 'context' => 'post'],
            'Page'        => ['label' => 'Halaman',          'context' => 'page'],
            'MenuDisplay' => ['label' => 'Menu Kantin',      'context' => 'kantin'],
            'Barang'      => ['label' => 'Produk PoS',       'context' => 'pos'],
            'CanteenMenu' => ['label' => 'Menu Belanja',     'context' => 'belanja'],
        ];

        // Usages dari arsip_usages table (Post, Page, dll)
        $tracked = $arsip->usages()
            ->with('usable')
            ->get()
            ->map(function ($usage) use ($typeMap) {
                $usable = $usage->usable;
                $type   = class_basename($usage->usable_type);
                $meta   = $typeMap[$type] ?? ['label' => $type, 'context' => 'other'];

                $name = $usable
                    ? ($usable->title ?? $usable->nama_barang ?? $usable->nama ?? $usable->name ?? 'Unknown')
                    : 'Item dihapus';

                $url = null;
                if ($usable) {
                    if ($type === 'Post') {
                        $url = route('admin.posts.edit', $usable->id);
                    } elseif ($type === 'Page') {
                        $url = route('admin.pages.edit', $usable->id);
                    }
                }

                return [
                    'type'    => $type,
                    'label'   => $meta['label'],
                    'context' => $meta['context'],
                    'name'    => $name,
                    'url'     => $url,
                    'field'   => $usage->field_name,
                ];
            });

        // Detect usage dari menu_displays (gambar barang kantin/PoS)
        // Path di arsip: "barangs/filename.jpg", di menu_displays.gambar: "/storage/barangs/filename.jpg"
        $storageUrl = '/storage/' . $arsip->path;
        $directUsages = collect();

        $menuDisplays = \App\Models\MenuDisplay::where('gambar', $storageUrl)
            ->with('barang:id,nama_barang,work_unit_id')
            ->get();

        foreach ($menuDisplays as $md) {
            $nama = $md->barang?->nama_barang ?? 'Produk tidak diketahui';
            $directUsages->push([
                'type'    => 'MenuDisplay',
                'label'   => 'Menu Kantin / PoS',
                'context' => 'kantin',
                'name'    => $nama,
                'url'     => null,
                'field'   => 'gambar',
            ]);
        }

        $usages = collect($tracked->all())->concat($directUsages)->values();

        return response()->json([
            'usages'       => $usages,
            'total_usages' => $usages->count(),
        ]);
    }

    /**
     * Determine jenis file dari mime type
     */
    private function determineJenis($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if ($mimeType === 'application/pdf') {
            return 'pdf';
        }

        if (in_array($mimeType, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ])) {
            return 'document';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        return 'other';
    }
}
