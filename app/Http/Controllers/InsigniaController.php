<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Insignia;

class InsigniaController extends Controller
{
    public function index()
    {
        $insignias = Insignia::all();
        return view('listados.insignias', compact('insignias'));
    }
}