<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Obtener el primer gym (Gym Principal)
    $gym = DB::table('gyms')->first();

    // Crear usuarios con gym_id
    DB::table('users')->insert([
      [
        'name' => 'Admin',
        'last_name' => 'Admin',
        'mother_last_name' => 'Admin',
        'phone' => '7770000006',
        'email' => 'admin@gmail.com',
        'email_verified_at' => '2024-01-17 04:50:32',
        'password' => Hash::make('12345678'),
        'role' => 'Admin',
        'gym_id' => $gym->id,
        'status' => true,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Miguel',
        'last_name' => 'Dev',
        'mother_last_name' => 'Devs',
        'phone' => '7770001236',
        'email' => 'miguel@gmail.com',
        'email_verified_at' => '2024-01-17 04:50:32',
        'password' => Hash::make('12345678'),
        'role' => 'Member',
        'gym_id' => $gym->id,
        'status' => true,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Moises',
        'last_name' => 'Dev',
        'mother_last_name' => 'Devs',
        'phone' => '7770001237',
        'email' => 'moi@gmail.com',
        'email_verified_at' => '2024-01-17 04:50:32',
        'password' => Hash::make('12345678'),
        'role' => 'Member',
        'gym_id' => $gym->id,
        'status' => true,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Moises',
        'last_name' => 'Dev',
        'mother_last_name' => 'Devs',
        'phone' => '7770001230',
        'email' => 'user@gmail.com',
        'email_verified_at' => '2024-01-17 04:50:32',
        'password' => Hash::make('12345678'),
        'role' => 'Member',
        'gym_id' => $gym->id,
        'status' => true,
        'created_at' => now(),
        'updated_at' => now()
      ]
    ]);

    // Asignar roles
    $user1 = User::where('email', 'admin@gmail.com')->first();
    $user1->assignRole('Admin');

    $user2 = User::where('email', 'miguel@gmail.com')->first();
    $user2->assignRole('Member');

    $user3 = User::where('email', 'moi@gmail.com')->first();
    $user3->assignRole('Member');

    // Asignar permisos
    $perfil = Role::where('name', 'Admin')->first();
    $perfil->givePermissionTo(Permission::where('module_key', 'users')->get());
  }
}
