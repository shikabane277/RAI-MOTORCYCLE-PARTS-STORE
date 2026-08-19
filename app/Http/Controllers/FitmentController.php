<?php

namespace App\Http\Controllers;

use App\Models\MotorcycleModel;
use Illuminate\Http\Request;

class FitmentController extends Controller
{
    public function index()
    {
        $makes = MotorcycleModel::where('is_active', true)
                                ->distinct()
                                ->orderBy('make')
                                ->pluck('make');

        $sessionFitment = session('fitment');

        return view('fitment', compact('makes', 'sessionFitment'));
    }

    public function makes()
    {
        $makes = MotorcycleModel::where('is_active', true)
                                ->distinct()
                                ->orderBy('make')
                                ->pluck('make');
        return response()->json($makes);
    }

    public function models(Request $request)
    {
        $request->validate(['make' => 'required|string']);
        $models = MotorcycleModel::where('is_active', true)
                                 ->where('make', $request->make)
                                 ->distinct()
                                 ->orderBy('model')
                                 ->pluck('model');
        return response()->json($models);
    }

    public function years(Request $request)
    {
        $request->validate(['make' => 'required|string', 'model' => 'required|string']);

        $entries = MotorcycleModel::where('is_active', true)
                                  ->where('make', $request->make)
                                  ->where('model', $request->model)
                                  ->get(['id', 'year_start', 'year_end']);

        $result = [];
        foreach ($entries as $entry) {
            $start = $entry->year_start ?? 2000;
            $end   = $entry->year_end ?? date('Y');
            for ($y = $start; $y <= $end; $y++) {
                $result[] = ['year' => $y, 'model_id' => $entry->id];
            }
        }

        return response()->json($result);
    }

    public function setSession(Request $request)
    {
        $request->validate(['make' => 'required', 'model' => 'required', 'year' => 'required', 'model_id' => 'required|exists:motorcycle_models,id']);

        $moto = MotorcycleModel::find($request->model_id);
        session(['fitment' => [
            'id'    => $moto->id,
            'make'  => $moto->make,
            'model' => $moto->model,
            'year'  => $request->year,
            'label' => "{$moto->make} {$moto->model} ({$request->year})",
        ]]);

        return response()->json(['success' => true, 'label' => session('fitment.label')]);
    }

    public function clearSession()
    {
        session()->forget('fitment');
        return response()->json(['success' => true]);
    }
}
