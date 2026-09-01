<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AraucariaObservation;

class DashboardController extends Controller
{
    public function index()
    {
        $observations = AraucariaObservation::with('user')->latest()->paginate(10);
        $myObservations = auth()->user() ? auth()->user()->araucariaObservations()->latest()->get() : collect();
        return view('dashboard', compact('observations', 'myObservations'));
    }

    public function feedPartial()
    {
        $observations = AraucariaObservation::with('user')->latest()->paginate(10);
        return view('components.araucaria.feed', compact('observations'));
    }

    public function myObsPartial()
    {
        $myObservations = auth()->user() ? auth()->user()->araucariaObservations()->latest()->get() : collect();
        $observations = $myObservations;
        return view('components.araucaria.tabela-registros', compact('observations', 'myObservations'));
    }
}
