<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductVariant::with('product')->where('is_active', true);

        if ($request->filter === 'low') {
            $query->whereRaw('stock_qty > 0 AND stock_qty <= low_stock_threshold');
        } elseif ($request->filter === 'out') {
            $query->where('stock_qty', 0);
        }

        if ($request->search) {
            $query->where('variant_sku', 'like', "%{$request->search}%")
                  ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $variants = $query->paginate(30)->withQueryString();
        $logs     = InventoryLog::with(['variant.product', 'createdBy'])->latest('created_at')->take(20)->get();

        return view('admin.inventory.index', compact('variants', 'logs'));
    }

    public function adjust(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'change_qty' => 'required|integer|not_in:0',
            'reason'     => 'required|in:restock,manual_adjustment,damaged,recount',
            'reference'  => 'nullable|string|max:100',
        ]);

        $newStock = max(0, $variant->stock_qty + $validated['change_qty']);
        $variant->update(['stock_qty' => $newStock]);

        InventoryLog::create([
            'product_variant_id' => $variant->id,
            'change_qty'         => $validated['change_qty'],
            'stock_after'        => $newStock,
            'reason'             => $validated['reason'],
            'reference'          => $validated['reference'] ?? 'Manual adjustment',
            'created_by'         => auth()->id(),
            'created_at'         => now(),
        ]);

        return back()->with('success', "Stock updated for {$variant->variant_sku}.");
    }
}
