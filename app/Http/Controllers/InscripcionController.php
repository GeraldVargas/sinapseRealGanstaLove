<?php

namespace App\Http\Controllers;

// IMPORTACIONES NECESARIAS
use App\Models\Inscripcion; // <-- Asegúrate de que esta línea esté aquí.
use Illuminate\Http\Request;
use Illuminate\View\View;

class InscripcionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // 1. Llama al modelo para obtener todos los datos.
        $inscripciones = Inscripcion::all();

        // 2. Retorna la vista y le pasa los datos.
        // Esta es la sintaxis estándar y más legible.
        return view('inscripciones.index', ['inscripciones' => $inscripciones]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
