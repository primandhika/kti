<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuItemController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::with(['children', 'page'])
            ->parents()
            ->orderBy('order')
            ->get()
            ->map(function ($item) {
                return $this->formatMenuItem($item);
            });

        $pages = Page::where('status', 'published')
            ->orderBy('title')
            ->get(['id', 'title', 'slug']);

        return Inertia::render('Admin/Menu/Index', [
            'menuItems' => $menuItems,
            'pages' => $pages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'type' => 'required|in:internal,external,page',
            'page_id' => 'nullable|exists:pages,id',
            'parent_id' => 'nullable|exists:menu_items,id',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'open_new_tab' => 'boolean',
            'icon' => 'nullable|string|max:100',
        ]);

        $validated['order'] = $validated['order'] ?? MenuItem::max('order') + 1;

        MenuItem::create($validated);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $menuItem = MenuItem::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'type' => 'required|in:internal,external,page',
            'page_id' => 'nullable|exists:pages,id',
            'parent_id' => 'nullable|exists:menu_items,id',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'open_new_tab' => 'boolean',
            'icon' => 'nullable|string|max:100',
        ]);

        $menuItem->update($validated);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diupdate');
    }

    public function destroy($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $menuItem->delete();

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil dihapus');
    }

    public function updateOrder(Request $request)
    {
        $items = $request->input('items', []);

        foreach ($items as $index => $item) {
            MenuItem::where('id', $item['id'])->update([
                'order' => $index,
                'parent_id' => $item['parent_id'] ?? null,
            ]);
        }

        return response()->json(['success' => true]);
    }

    private function formatMenuItem($item)
    {
        return [
            'id' => $item->id,
            'label' => $item->label,
            'url' => $item->url,
            'type' => $item->type,
            'page_id' => $item->page_id,
            'page' => $item->page ? [
                'id' => $item->page->id,
                'title' => $item->page->title,
                'slug' => $item->page->slug,
            ] : null,
            'parent_id' => $item->parent_id,
            'order' => $item->order,
            'is_active' => $item->is_active,
            'open_new_tab' => $item->open_new_tab,
            'icon' => $item->icon,
            'children' => $item->children->map(function ($child) {
                return $this->formatMenuItem($child);
            }),
        ];
    }
}
