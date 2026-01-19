<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sedes (branches/locations)
        if (!Schema::hasTable('tenant_sedes')) {
            Schema::create('tenant_sedes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                $table->string('nombre');
                $table->string('direccion')->nullable();
                $table->string('telefono')->nullable();
                $table->string('ciudad')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        // Carreras (programs/courses)
        if (!Schema::hasTable('tenant_carreras')) {
            Schema::create('tenant_carreras', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                $table->string('nombre');
                $table->text('descripcion')->nullable();
                $table->string('duracion')->nullable(); // e.g., "5 años", "10 semestres"
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        // Pivot: Carreras <-> Sedes (many-to-many)
        if (!Schema::hasTable('carrera_sede')) {
            Schema::create('carrera_sede', function (Blueprint $table) {
                $table->id();
                $table->foreignId('carrera_id')->constrained('tenant_carreras')->onDelete('cascade');
                $table->foreignId('sede_id')->constrained('tenant_sedes')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['carrera_id', 'sede_id']);
            });
        }

        // Ofertas (scholarships, discounts, promo)
        if (!Schema::hasTable('tenant_ofertas')) {
            Schema::create('tenant_ofertas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                $table->string('nombre');
                $table->string('tipo'); // beca, descuento, preuniversitario, convenio
                $table->text('descripcion')->nullable();
                $table->date('fecha_inicio')->nullable();
                $table->date('fecha_fin')->nullable();
                $table->boolean('permanente')->default(false);
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        // Eventos (congresses, open days, fairs)
        if (!Schema::hasTable('tenant_eventos')) {
            Schema::create('tenant_eventos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                $table->string('nombre');
                $table->text('descripcion')->nullable();
                $table->date('fecha_inicio')->nullable();
                $table->date('fecha_fin')->nullable();
                $table->string('lugar')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_eventos');
        Schema::dropIfExists('tenant_ofertas');
        Schema::dropIfExists('carrera_sede');
        Schema::dropIfExists('tenant_carreras');
        Schema::dropIfExists('tenant_sedes');
    }
};
