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
        if (Schema::hasTable('tenants')) {
            Schema::table('tenants', function (Blueprint $table) {
                if (!Schema::hasColumn('tenants', 'whatsapp_token')) {
                    $table->string('whatsapp_token')->nullable()->after('status');
                }
                if (!Schema::hasColumn('tenants', 'whatsapp_phone_id')) {
                    $table->string('whatsapp_phone_id')->nullable()->after('whatsapp_token');
                }
                if (!Schema::hasColumn('tenants', 'whatsapp_business_account_id')) {
                    $table->string('whatsapp_business_account_id')->nullable()->after('whatsapp_phone_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tenants')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn(['whatsapp_token', 'whatsapp_phone_id', 'whatsapp_business_account_id']);
            });
        }
    }
};
