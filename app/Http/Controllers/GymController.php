<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use Illuminate\Http\Request;
use App\Http\Requests\StoreGymRequest;
use App\Http\Requests\UpdateGymRequest;
use Inertia\Inertia;

class GymController extends Controller {
    // Guarda el nombre de la ruta para el formulario
    private string $routeName; // Nombre de la ruta para el formulario
    protected string $module = 'gym.'; // Nombre del módulo para los permisos

    // Constructor que verifica si el usuario tiene permisos para acceder a la ruta
    public function __construct() {
        $this->middleware('auth'); // Requiere autenticación
        $this->routeName = 'gym.'; // Define el nombre de la ruta para el formulario

        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);  
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);  
        $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Gym::query();

        if ($request->has('search') && $request->search !== null) { // Filtrar si hay búsqueda
            $query->where('name', 'like', '%' . $request->search . '%'); // Filtra por nombre
            $query->orWhere('address', 'like', '%' . $request->search . '%'); // Filtra por correo
            $query->orWhere('phone', 'like', '%' . $request->search . '%'); // Filtra por telefono
        }

        $gyms = $query->orderBy('id', 'desc')->paginate(8)->withQueryString(); // Pagina resultados

        return Inertia::render('Gym/Index', [
            'title' => 'Lista de Gimnasios',
            'gyms' => $gyms,
            'routeName' => $this->routeName,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        return Inertia::render('Gym/Create', [
            'title' => 'Crear Registro de Gimnasio',
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGymRequest $request) {
        Gym::create($request->validated());
        return redirect()->route($this->routeName . 'index')->with('success', 'Registro creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gym $gym)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gym $gym) {
        return Inertia::render('Gym/Edit', [
            'title' => 'Editar Registro de Gimnasio',
            'gym' => $gym,
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGymRequest $request, Gym $gym) {
        Gym::where('id', $gym->id)->update($request->validated());
        return redirect()->route($this->routeName . 'index')->with('success', 'Registro actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gym $gym)
    {
        $gym->delete();
        return redirect()->route($this->routeName . 'index')->with('success', 'Gimnasio eliminado correctamente');
    }
}
