<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class MigrateContactsToTenantSeeder extends Seeder
{
    /**
     * Migrate existing contacts to UNITEPC tenant
     */
    public function run(): void
    {
        // Find UNITEPC tenant (first non-system tenant)
        $unitepcTenant = Tenant::where('name', '!=', 'SOLVEIT System')->first();

        if (!$unitepcTenant) {
            $this->command->error('No tenant found to migrate contacts to.');
            return;
        }

        // Update all contacts without tenant_id
        $updated = Contact::whereNull('tenant_id')->update([
            'tenant_id' => $unitepcTenant->id,
        ]);

        $this->command->info("Migrated {$updated} contacts to tenant: {$unitepcTenant->name}");
    }
}
