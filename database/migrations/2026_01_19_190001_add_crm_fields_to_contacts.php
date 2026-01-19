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
        // Add CRM fields to contacts
        if (Schema::hasTable('contacts') && !Schema::hasColumn('contacts', 'nivel_interes')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->enum('nivel_interes', ['frio', 'tibio', 'caliente'])->default('frio')->after('tenant_id');
                $table->enum('estado_crm', ['prospecto', 'interesado', 'inscrito', 'perdido'])->default('prospecto')->after('nivel_interes');
                $table->text('notas_crm')->nullable()->after('estado_crm');
            });
        }

        // Contact <-> Carreras (interest)
        if (!Schema::hasTable('contact_carrera')) {
            Schema::create('contact_carrera', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contact_id')->constrained()->onDelete('cascade');
                $table->foreignId('carrera_id')->constrained('tenant_carreras')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['contact_id', 'carrera_id']);
            });
        }

        // Contact <-> Sedes (interest)
        if (!Schema::hasTable('contact_sede')) {
            Schema::create('contact_sede', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contact_id')->constrained()->onDelete('cascade');
                $table->foreignId('sede_id')->constrained('tenant_sedes')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['contact_id', 'sede_id']);
            });
        }

        // Contact <-> Ofertas (applied/assigned)
        if (!Schema::hasTable('contact_oferta')) {
            Schema::create('contact_oferta', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contact_id')->constrained()->onDelete('cascade');
                $table->foreignId('oferta_id')->constrained('tenant_ofertas')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['contact_id', 'oferta_id']);
            });
        }

        // Contact <-> Eventos (registered)
        if (!Schema::hasTable('contact_evento')) {
            Schema::create('contact_evento', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contact_id')->constrained()->onDelete('cascade');
                $table->foreignId('evento_id')->constrained('tenant_eventos')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['contact_id', 'evento_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_evento');
        Schema::dropIfExists('contact_oferta');
        Schema::dropIfExists('contact_sede');
        Schema::dropIfExists('contact_carrera');

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['nivel_interes', 'estado_crm', 'notas_crm']);
        });
    }
};
