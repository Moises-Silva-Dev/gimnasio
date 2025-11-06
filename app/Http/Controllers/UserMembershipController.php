<?php

namespace App\Http\Controllers;

use App\Models\UserMembership;
use App\Models\User;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Http\Requests\UserMembership\StoreUserMembershipRequest;
use Inertia\Inertia;
use Carbon\Carbon;

class UserMembershipController extends Controller
{
    protected string $routeName;
    protected string $source;
    protected string $module = 'user-memberships';

    public function __construct()
    {
        $this->middleware('auth');
        $this->routeName = 'user-memberships.';
        $this->source = 'UserMembership/';
        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = UserMembership::with(['user', 'membership.gyms']);

        // Filtros
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'expired') {
                $query->expired();
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('gym_id')) {
            $query->whereHas('membership', function ($q) use ($request) {
                $q->where('gym_id', $request->gym_id);
            });
        }

        $userMemberships = $query->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title' => 'Membresías de Usuarios',
            'userMemberships' => $userMemberships,
            'routeName' => $this->routeName,
            'filters' => $request->only(['search', 'status', 'gym_id']),
        ]);
    }

    public function create()
    {
        // Solo usuarios con rol Member
        $users = User::role('Member')
            ->select('id', 'name', 'last_name', 'email')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => "{$user->name} {$user->last_name} - {$user->email}",
                ];
            });

        $memberships = Membership::with('gyms:id,name')
            ->get()
            ->map(function ($membership) {
                return [
                    'id' => $membership->id,
                    'name' => $membership->name,
                    'gym_name' => $membership->gyms->name ?? 'N/A',
                    'duration_days' => $membership->duration_days,
                    'sessions' => $membership->sessions,
                    'price' => $membership->price,
                    'description' => $membership->description,
                ];
            });

        return Inertia::render("{$this->source}Create", [
            'title' => 'Asignar Membresía a Usuario',
            'routeName' => $this->routeName,
            'users' => $users,
            'memberships' => $memberships,
        ]);
    }

    public function store(StoreUserMembershipRequest $request)
    {
        $membership = Membership::findOrFail($request->membership_id);

        $startDate = Carbon::parse($request->start_date);
        $endDate = $startDate->copy()->addDays($membership->duration_days);

        UserMembership::create([
            'user_id' => $request->user_id,
            'membership_id' => $request->membership_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'remaining_sessions' => $membership->sessions,
            'status' => 'active',
            'payment_amount' => $request->payment_amount,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        return redirect()
            ->route("{$this->routeName}index")
            ->with('success', '¡Membresía asignada exitosamente!');
    }

    public function show(UserMembership $userMembership)
    {
        $userMembership->load(['user', 'membership.gyms']);

        return Inertia::render("{$this->source}Show", [
            'title' => 'Detalle de Membresía',
            'userMembership' => $userMembership,
            'routeName' => $this->routeName,
        ]);
    }

    public function edit(UserMembership $userMembership)
    {
        $userMembership->load(['user', 'membership']);

        return Inertia::render("{$this->source}Edit", [
            'title' => 'Editar Membresía',
            'userMembership' => $userMembership,
            'routeName' => $this->routeName,
        ]);
    }

    public function update(Request $request, UserMembership $userMembership)
    {
        $validated = $request->validate([
            'remaining_sessions' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,expired,cancelled'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $userMembership->update($validated);

        return redirect()
            ->route("{$this->routeName}index")
            ->with('success', '¡Membresía actualizada correctamente!');
    }

    public function destroy(UserMembership $userMembership)
    {
        $userMembership->delete();

        return redirect()
            ->route("{$this->routeName}index")
            ->with('success', '¡Membresía eliminada exitosamente!');
    }

    // Método adicional para decrementar sesión
    public function decrementSession(UserMembership $userMembership)
    {
        // El modelo se encarga de auto-resetear si es necesario
        $userMembership->decrementSession();

        return back()->with('success', 'Sesión descontada correctamente.');
    }

    // Método para cancelar membresía
    public function cancel(UserMembership $userMembership)
    {
        $userMembership->cancel();

        return back()->with('success', 'Membresía cancelada exitosamente.');
    }
}
