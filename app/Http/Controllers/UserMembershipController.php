<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserMembership\StoreUserMembershipRequest;
use App\Http\Requests\UserMembership\UpdateUserMembershipRequest;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class UserMembershipController extends Controller
{
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
        $query = UserMembership::with(['gym', 'user', 'membership.gyms', 'payment', 'createdBy']);

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $userMemberships = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title' => 'Lista de Membresías de Usuarios',
            'userMemberships' => $userMemberships,
            'routeName' => $this->routeName,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title' => 'Crear Membresía de Usuario',
            'routeName' => $this->routeName,
            'memberships' => Membership::select('id', 'name', 'price', 'duration_days', 'gym_id', 'sessions')->get(),
            'gyms' => Gym::orderBy('name')->select('id', 'name')->get(),
        ]);
    }

    public function store(StoreUserMembershipRequest $request)
    {
        try {
            UserMembership::create($request->validated());

            return redirect()->route("{$this->routeName}index")
                ->with('success', 'Membresía de usuario creada con éxito.');
        } catch (\Exception $e) {
            Log::error('Error al crear membresía:', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la membresía: ' . $e->getMessage());
        }
    }

    public function show(UserMembership $userMembership)
    {
        $userMembership->load(['gym', 'user', 'membership.gyms', 'payment', 'createdBy']);

        return Inertia::render("{$this->source}Show", [
            'title' => 'Detalles de Membresía',
            'routeName' => $this->routeName,
            'userMembership' => $userMembership,
        ]);
    }

    public function edit(UserMembership $userMembership)
    {
        $userMembership->load(['user', 'membership', 'gym']);

        return Inertia::render("{$this->source}Edit", [
            'title' => 'Editar Membresía de Usuario',
            'routeName' => $this->routeName,
            'userMembership' => $userMembership,
            'users' => User::select('id', 'name', 'email')->get(),
            'memberships' => Membership::select('id', 'name', 'price', 'duration_days', 'gym_id')->get(),
            'gyms' => Gym::select('id', 'name')->get(),
        ]);
    }

    public function update(UpdateUserMembershipRequest $request, UserMembership $userMembership)
    {
        try {
            $userMembership->update($request->validated());

            return redirect()->route("{$this->routeName}index")
                ->with('success', 'Membresía actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la membresía: ' . $e->getMessage());
        }
    }

    public function destroy(UserMembership $userMembership)
    {
        try {
            $userMembership->delete();

            return redirect()->route("{$this->routeName}index")
                ->with('success', 'Membresía eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la membresía: ' . $e->getMessage());
        }
    }

    /**
     * Buscar usuarios miembros por gimnasio
     */
    public function searchMembers(Request $request, $gymId)
    {
        $query = $request->input('q');

        $users = User::where('gym_id', $gymId)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('mother_last_name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'last_name', 'mother_last_name', 'email')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    /**
     * Descontar una sesión de la membresía
     */
    public function decrementSession(UserMembership $userMembership)
    {
        if ($userMembership->remaining_sessions > 0) {
            $userMembership->decrement('remaining_sessions');

            return redirect()->back()
                ->with('success', 'Sesión descontada correctamente.');
        }

        return redirect()->back()
            ->with('error', 'No quedan sesiones disponibles.');
    }

    /**
     * Cancelar membresía
     */
    public function cancel(UserMembership $userMembership)
    {
        $userMembership->update(['status' => 'cancelled']);

        return redirect()->back()
            ->with('success', 'Membresía cancelada correctamente.');
    }

    /**
     * Verificar el estado de membresía de un usuario en un gimnasio
     */
    public function checkStatus(Request $request)
    {
        $userId = $request->input('user_id');
        $gymId = $request->input('gym_id');

        $activeMembership = UserMembership::where('user_id', $userId)
            ->where('gym_id', $gymId)
            ->whereIn('status', ['active', 'pending'])
            ->with('membership')
            ->first();

        if ($activeMembership) {
            return response()->json([
                'has_active_membership' => true,
                'current_status' => $activeMembership->status,
                'membership_name' => $activeMembership->membership->name,
                'start_date' => $activeMembership->start_date,
                'end_date' => $activeMembership->end_date,
            ]);
        }

        return response()->json([
            'has_active_membership' => false,
        ]);
    }
}
