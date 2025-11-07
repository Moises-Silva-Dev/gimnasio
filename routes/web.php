<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\GymController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserMembershipController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Users
    Route::resource('users', controller: UserController::class);
    Route::resource('roles', RoleController::class)->names('roles');
    Route::resource('permissions', PermissionController::class);
    Route::resource('modules', ModuleController::class);

    Route::post('/user/set-active-role', [AuthenticatedSessionController::class, 'setActiveRole'])
        ->name('user.set-active-role');

    Route::get('/select-role', function () {
        return Inertia::render('Auth/RoleSelect');
    })->name('role.select');

    // Gym
    Route::resource('gym', controller: GymController::class);

    // Memberships
    Route::resource('membership', controller: MembershipController::class);

    // User Memberships
    Route::resource('user-memberships', controller: UserMembershipController::class);

    // Rutas adicionales para User Memberships
    Route::get('/gyms/{gym}/members/search', [UserMembershipController::class, 'searchMembers'])
        ->name('gyms.members.search');

    Route::post('/user-memberships/{userMembership}/decrement-session', [UserMembershipController::class, 'decrementSession'])
        ->name('user-memberships.decrement-session');

    Route::post('/user-memberships/{userMembership}/cancel', [UserMembershipController::class, 'cancel'])
        ->name('user-memberships.cancel');

    Route::get('/user-memberships/check-status', [UserMembershipController::class, 'checkStatus'])
        ->name('user-memberships.check-status');
});

require __DIR__ . '/auth.php';
