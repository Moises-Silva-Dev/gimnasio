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
    DB::table('users')->insert(['name' => 'Admin', 'last_name' => 'Admin', 'mother_last_name' => 'Admin', 'numero' => '7770000006', 'email' => 'admin@gmail.com', 'email_verified_at' => '2024-01-17 04:50:32', 'password' => Hash::make('12345678'), 'role' => 'Admin']);
    DB::table('users')->insert(['name' => 'Miguel', 'last_name' => 'Dev', 'mother_last_name' => 'Devs', 'numero' => '7770001236', 'email' => 'miguel@gmail.com', 'email_verified_at' => '2024-01-17 04:50:32', 'password' => Hash::make('12345678'), 'role' => 'Member']);
    DB::table('users')->insert(values: ['name' => 'Moises', 'last_name' => 'Dev', 'mother_last_name' => 'Devs', 'numero' => '7770001237', 'email' => 'moi@gmail.com', 'email_verified_at' => '2024-01-17 04:50:32', 'password' => Hash::make('12345678'), 'role' => 'Member']);
    DB::table('users')->insert(values: ['name' => 'Moises', 'last_name' => 'Dev', 'mother_last_name' => 'Devs', 'numero' => '7770001230', 'email' => 'user@gmail.com', 'email_verified_at' => '2024-01-17 04:50:32', 'password' => Hash::make('12345678'), 'role' => 'Member']);



    $user1 = User::where('email', 'admin@gmail.com')->first();
    $user1->assignRole('Admin');

    $user2 = User::where('email', 'miguel@gmail.com')->first();
    $user2->assignRole('Member');
    $user3 = User::where('email', 'moi@gmail.com')->first();
    $user3->assignRole('Member');



    //Aca aignamos las variables para permisos
    $perfil = Role::where('name', 'Admin')->first();
    $Procesos = Role::where('name', operator: 'Procesos')->first();






    //todos los permisos a Usuarios
    $perfil->givePermissionTo(Permission::where('module_key', 'users')->get());








    //Permisos a Usuario-Sistema

    /*
      $usuarioSistema->givePermissionTo(Permission::where('name', 'documento.index')->first());
      $usuarioSistema->givePermissionTo(Permission::where('name', 'documento.store')->first());
      $usuarioSistema->givePermissionTo(Permission::where('name', 'documento.update')->first());
      $usuarioSistema->givePermissionTo(Permission::where('name', 'documento.delete')->first());

      $usuarioSistema->givePermissionTo(Permission::where('name', 'documentoLegal.index')->first());
      $usuarioSistema->givePermissionTo(Permission::where('name', 'documentoLegal.store')->first());
      $usuarioSistema->givePermissionTo(Permission::where('name', 'documentoLegal.update')->first());
      $usuarioSistema->givePermissionTo(Permission::where('name', 'documentoLegal.delete')->first());
      */


  }
}
