<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AraucariaObservation;

class DashboardController extends Controller
{
    public function index()
    {
        $observations = AraucariaObservation::with('user')->latest()->get();
        return view('dashboard', compact('observations'));
    }
}
