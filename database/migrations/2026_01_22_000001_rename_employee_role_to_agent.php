<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update the 'employee' role to 'agent' (Agente)
     */
    public function up(): void
    {
        Role::where('name', 'employee')->update([
            'name' => 'agent',
            'label' => 'Agente'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::where('name', 'agent')->update([
            'name' => 'employee',
            'label' => 'Empleado'
        ]);
    }
};
