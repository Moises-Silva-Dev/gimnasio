<?php

namespace App\Http\Controllers;

use App\Models\Gimnasio;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GimnasioController extends Controller
{
    // Guarda el nombre de la ruta para el formulario
    private string $routeName; // Nombre de la ruta para el formulario
    protected string $module = 'gimnasio.'; // Nombre del módulo para los permisos

    // Constructor que verifica si el usuario tiene permisos para acceder a la ruta
    public function __construct() {
        $this->middleware('auth'); // Requiere autenticación
        $this->routeName = 'gimnasio.'; // Define el nombre de la ruta para el formulario

        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);  
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);  
        $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
    }

    public function index(Request $request) {
        $query = Gimnasio::query();

        if ($request->has('search') && $request->search !== null) { // Filtrar si hay búsqueda
            $query->where('nombre', 'like', '%' . $request->search . '%'); // Filtra por nombre
            $query->orWhere('email', 'like', '%' . $request->search . '%'); // Filtra por dirección
            $query->orWhere('colonia', 'like', '%' . $request->search . '%'); // Filtra por dirección
            $query->orWhere('municipio', 'like', '%' . $request->search . '%'); // Filtra por ciudad
        }

        $gimnasios = $query->orderBy('id', 'desc')->paginate(8)->withQueryString(); // Pagina resultados

        return Inertia::render("Gimnasio/Index", [
            'titulo' => 'Lista de Gimnasios',
            'gimnasios' => $gimnasios,
            'routeName' => $this->routeName,
            'filters' => $request->only(['search']),
        ]);
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
    public function show(Gimnasio $gimnasio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gimnasio $gimnasio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gimnasio $gimnasio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gimnasio $gimnasio)
    {
        //
    }
}
