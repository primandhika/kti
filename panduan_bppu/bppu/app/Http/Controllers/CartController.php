<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CanteenMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    private function getOrCreateCart(Request $request)
    {
        $sessionId = $request->session()->get('cart_session_id');

        if (!$sessionId) {
            $sessionId = Str::uuid()->toString();
            $request->session()->put('cart_session_id', $sessionId);
        }

        $cart = Cart::where('session_id', $sessionId)->first();

        if (!$cart) {
            $cart = Cart::create([
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
                'expires_at' => now()->addDays(7),
            ]);
        }

        return $cart;
    }

    public function getCart(Request $request)
    {
        $cart = $this->getOrCreateCart($request);
        $cart->load(['items.canteenMenu.category']);

        return response()->json([
            'cart' => $cart,
            'total' => $cart->total,
            'total_items' => $cart->total_items,
        ]);
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'canteen_menu_id' => 'required|exists:canteen_menus,id',
            'quantity' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = $this->getOrCreateCart($request);
        $menu = CanteenMenu::findOrFail($request->canteen_menu_id);

        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('canteen_menu_id', $menu->id)
            ->first();

        if ($existingItem) {
            $existingItem->quantity += $request->input('quantity', 1);
            if ($request->notes) {
                $existingItem->notes = $request->notes;
            }
            $existingItem->save();
            $item = $existingItem;
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'canteen_menu_id' => $menu->id,
                'quantity' => $request->input('quantity', 1),
                'price' => $menu->price,
                'notes' => $request->notes,
            ]);
        }

        $cart->load(['items.canteenMenu.category']);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil ditambahkan ke keranjang',
            'cart' => $cart,
            'total' => $cart->total,
            'total_items' => $cart->total_items,
        ]);
    }

    public function updateItem(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $cartItem->update([
            'quantity' => $request->quantity,
            'notes' => $request->notes,
        ]);

        $cart = $cartItem->cart;
        $cart->load(['items.canteenMenu.category']);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil diupdate',
            'cart' => $cart,
            'total' => $cart->total,
            'total_items' => $cart->total_items,
        ]);
    }

    public function removeItem(CartItem $cartItem)
    {
        $cart = $cartItem->cart;
        $cartItem->delete();

        $cart->load(['items.canteenMenu.category']);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang',
            'cart' => $cart,
            'total' => $cart->total,
            'total_items' => $cart->total_items,
        ]);
    }

    public function clearCart(Request $request)
    {
        $cart = $this->getOrCreateCart($request);
        $cart->items()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan',
            'cart' => $cart,
            'total' => 0,
            'total_items' => 0,
        ]);
    }
}
