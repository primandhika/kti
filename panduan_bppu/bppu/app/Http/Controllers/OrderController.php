<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\CanteenMenu;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::where('is_active', true)
            ->with(['activeMenus' => function ($query) {
                $query->orderBy('display_order');
            }])
            ->orderBy('display_order')
            ->get();

        return Inertia::render('Order', [
            'categories' => $categories,
        ]);
    }
}
