<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CuentaController extends Controller {
    // Guarda el nombre de la ruta para el formulario
    private string $routeName; // Nombre de la ruta para el formulario
    protected string $module = 'cuenta.'; // Nombre del módulo para los permisos

    // Constructor que verifica si el usuario tiene permisos para acceder a la ruta
    public function __construct() {
        $this->middleware('auth'); // Requiere autenticación
        $this->routeName = 'cuenta.'; // Define el nombre de la ruta para el formulario

        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);  
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);  
        $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
    }

    public function index(Request $request) {
        $query = Cuenta::query();

        if ($request->has('search') && $request->search !== null) { // Filtrar si hay búsqueda
            $query->where('nombre_cuenta', 'like', '%' . $request->search . '%'); // Filtra por nombre
        }

        $cuentas = $query->orderBy('id', 'desc')->paginate(8)->withQueryString(); // Pagina resultados

        return Inertia::render("Cuenta/Index", [
            'titulo' => 'Lista de Cuentas',
            'cuentas' => $cuentas,
            'routeName' => $this->routeName,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("Cuenta/Create", [
            'titulo' => 'Agregar Cuenta',
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Cuenta $cuenta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cuenta $cuenta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cuenta $cuenta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cuenta $cuenta)
    {
        //
    }
}
