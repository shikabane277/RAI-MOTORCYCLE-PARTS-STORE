<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MotorcycleModel;
use App\Models\Product;
use Illuminate\Http\Request;

class FitmentController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('motorcycleModels')->active()->paginate(20);
        $motoModels = MotorcycleModel::where('is_active', true)->orderBy('make')->orderBy('model')->get();
        return view('admin.fitments.index', compact('products', 'motoModels'));
    }

    public function attach(Request $request, Product $product)
    {
        $request->validate(['motorcycle_model_id' => 'required|exists:motorcycle_models,id']);
        $product->motorcycleModels()->syncWithoutDetaching([
            $request->motorcycle_model_id => ['notes' => $request->notes]
        ]);
        return back()->with('success', 'Fitment attached.');
    }

    public function detach(Product $product, MotorcycleModel $model)
    {
        $product->motorcycleModels()->detach($model->id);
        return back()->with('success', 'Fitment removed.');
    }
}
