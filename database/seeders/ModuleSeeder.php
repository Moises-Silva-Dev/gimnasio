<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Seguridad',
                'key' => 'security',
                'description' => 'Módulo de gestión de seguridad y accesos'
            ],
            [
                'name' => 'Usuarios',
                'key' => 'users',
                'description' => 'Gestión de usuarios del sistema'
            ],
            [
                'name' => 'Roles',
                'key' => 'roles',
                'description' => 'Gestión de roles y permisos'
            ],           
            [
                'name' => 'Modulos',
                'key' => 'modules',
                'description' => 'Gestión de módulos'
            ],
            [
                'name' => 'Permisos',
                'key' => 'permissions',
                'description' => 'Gestión de permisos'
            ],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }
}