<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Roles
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['label' => 'Super Administrador']
        );

        $tenantAdminRole = Role::firstOrCreate(
            ['name' => 'tenant_admin'],
            ['label' => 'Administrador de Empresa']
        );

        $employeeRole = Role::firstOrCreate(
            ['name' => 'employee'],
            ['label' => 'Empleado']
        );

        // 2. Create System Tenant (Optional, for the owners)
        $systemTenant = Tenant::firstOrCreate(
            ['name' => 'SOLVEIT System'],
            ['status' => 'active']
        );

        // 3. Create Super Admin User
        $user = User::updateOrCreate(
            ['email' => 'juanjo270201@gmail.com'],
            [
                'name' => 'Juanjo (Super Admin)',
                'password' => Hash::make('jj270201Ju@n2001'),
                'role_id' => $superAdminRole->id,
                'tenant_id' => $systemTenant->id, // Or null if you prefer super admin to be tenant-less
            ]
        );

        $this->command->info('Super Admin user created: juanjo270201@gmail.com');
    }
}
