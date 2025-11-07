<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserMembership\StoreUserMembershipRequest;
use App\Http\Requests\UserMembership\UpdateUserMembershipRequest;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserMembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private string $routeName;
    private string $module;
    private string $source;
    public function __construct()
    {
        $this->routeName = 'user-memberships.';
        $this->module = 'user-memberships';
        $this->source = 'UserMembership/';

        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
    }
    public function index(Request $request)
    {
        $query = UserMembership::with(['gym', 'user', 'membership', 'payment', 'createdBy']);
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }
        $userMemberships = $query->orderBy('id', 'desc')->paginate(8)->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title' => 'Lista de Membresías de Usuarios',
            'userMemberships' => $userMemberships,
            'routeName' => $this->routeName,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title' => 'Crear Membresía de Usuario',
            'routeName' => $this->routeName,
            'users' => User::select('id', 'name', 'email', 'gym_id')->get(), // ← Agregar esto
            'memberships' => Membership::select('id', 'name', 'price', 'duration_days','gym_id')->get(),
            'gyms' => Gym::orderBy('name')->select('id', 'name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserMembershipRequest  $request)
    {
        UserMembership::create($request->validated());
        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Membresía de usuario creada con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserMembership $userMembership)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserMembership $userMembership)
    {
        return Inertia::render("{$this->source}Edit", [
            'title' => 'Editar Membresía de Usuario',
            'routeName' => $this->routeName,
            //'userMembership' => $userMembership,
            'users' => User::select('id', 'name')->get(),
            'memberships' => Membership::select('id', 'name', 'price')->get(),
            'gyms' => Gym::select('id', 'name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserMembershipRequest  $request, UserMembership $userMembership)
    {
        $userMembership->update($request->validated());

        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Membresía actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserMembership $userMembership)
    {
        $userMembership->delete();

        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Membresía eliminada correctamente.');
    }
    // En UserMembershipController.php
    public function searchUsers22(Request $request)
    {
        $query = User::where('role', 'Member'); // Filtra por rol Member

        if ($request->filled('gym_id')) {
            $query->where('gym_id', $request->gym_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        return response()->json([
            'users' => $query->limit(20)->get(['id', 'name', 'email', 'gym_id'])
        ]);
    }
    // En UserMembershipController o UserController
    public function searchUsers(Request $request)
    {
        $query = User::where('role', 'Member'); // Solo usuarios Member

        if ($request->filled('gym_id')) {
            $query->where('gym_id', $request->gym_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->select('id', 'name', 'email', 'gym_id')
            ->limit(20) // Limitar resultados
            ->get();

        return response()->json($users);
    }
    public function searchMembers(Request $request, $gymId)
    {
        $query = $request->get('q', '');

        $users = User::where('gym_id', $gymId)
            ->whereHas('roles', fn($q) => $q->where('name', 'Member'))
            ->where('name', 'like', "%{$query}%")
            ->select('id', 'name')
            ->orderBy('name')
            ->limit(20) // limita los resultados
            ->get();

        return response()->json($users);
    }
}
