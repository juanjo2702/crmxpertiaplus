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
        Schema::table('users', function (Blueprint $table) {
            // Nullable because Super Admin might not belong to a specific tenant
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');

            // Assuming we'll seed roles first so we can constrain.
            // If strictly necessary we can drop foreign key check but usually seeding first is cleaner.
            // For now, let's make it nullable to avoid issues with existing users if any,
            // though we'll likely seed the default roles.
            $table->foreignId('role_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');

            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
