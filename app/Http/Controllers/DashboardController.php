<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AraucariaObservation;

class DashboardController extends Controller
{
    public function index()
    {
        $observations = AraucariaObservation::with('user')->latest()->paginate(10);
        return view('dashboard', compact('observations'));
    }
}
