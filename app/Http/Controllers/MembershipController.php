<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Gym;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMembershipRequest;
use App\Http\Requests\UpdateMembershipRequest;
use Inertia\Inertia;

class MembershipController extends Controller {
    // Guarda el nombre de la ruta para el formulario
    private string $routeName; // Nombre de la ruta para el formulario
    protected string $module = 'membership.'; // Nombre del módulo para los permisos

    // Constructor que verifica si el usuario tiene permisos para acceder a la ruta
    public function __construct() {
        $this->middleware('auth'); // Requiere autenticación
        $this->routeName = 'membership.'; // Define el nombre de la ruta para el formulario

        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);  
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);  
        $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Membership::with('gyms');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%')
                ->orWhereHas('gyms', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%'); 
                });
        }
        
        $memberships = $query->orderBy('id', 'desc')->paginate(8)->withQueryString(); 

        return Inertia::render('Membership/Index', [
            'title' => 'Lista de Membresías',
            'memberships' => $memberships,
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
    public function show(Membership $membership)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Membership $membership)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Membership $membership)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Membership $membership)
    {
        $membership->delete();
        return redirect()->route($this->routeName . 'index')->with('success', 'Membresía eliminada con éxito.');
    }
}
