<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantReportController extends Controller
{
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;

        // Total contacts
        $totalContacts = Contact::where('tenant_id', $tenantId)->count();

        // Connect contacts count
        $connectedContacts = Contact::where('tenant_id', $tenantId)
            ->whereNotNull('wa_id')
            ->count();

        // By Interest Level
        $byInterest = Contact::where('tenant_id', $tenantId)
            ->select('nivel_interes', DB::raw('count(*) as count'))
            ->groupBy('nivel_interes')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->nivel_interes ? ucfirst($item->nivel_interes) : 'Sin asignar',
                    'value' => $item->count,
                    'color' => match ($item->nivel_interes) {
                        'frio' => 'bg-blue-500',
                        'tibio' => 'bg-orange-500',
                        'caliente' => 'bg-red-500',
                        default => 'bg-slate-500'
                    }
                ];
            });

        // By CRM Status
        $byStatus = Contact::where('tenant_id', $tenantId)
            ->select('estado_crm', DB::raw('count(*) as count'))
            ->groupBy('estado_crm')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->estado_crm ? ucfirst($item->estado_crm) : 'Sin estado',
                    'value' => $item->count,
                    'color' => match ($item->estado_crm) {
                        'prospecto' => 'bg-indigo-500',
                        'interesado' => 'bg-yellow-500',
                        'inscrito' => 'bg-green-500',
                        'perdido' => 'bg-gray-500',
                        default => 'bg-slate-500'
                    }
                ];
            });

        // Top 5 Carreras logic with check for table existence (handling migration failure case gracefully)
        try {
            $topCarreras = DB::table('contact_carreras')
                ->join('contacts', 'contact_carreras.contact_id', '=', 'contacts.id')
                ->join('tenant_carreras', 'contact_carreras.carrera_id', '=', 'tenant_carreras.id')
                ->where('contacts.tenant_id', $tenantId)
                ->select('tenant_carreras.nombre', DB::raw('count(*) as count'))
                ->groupBy('tenant_carreras.id', 'tenant_carreras.nombre')
                ->orderByDesc('count')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            $topCarreras = [];
        }

        return Inertia::render('Tenant/Reports/Index', [
            'overview' => [
                'total_contacts' => $totalContacts,
                'connected_contacts' => $connectedContacts,
                'conversion_rate' => $totalContacts > 0 ? round(($byStatus->where('label', 'Inscrito')->first()['value'] ?? 0) / $totalContacts * 100, 1) : 0
            ],
            'charts' => [
                'interest' => $byInterest,
                'status' => $byStatus,
                'carreras' => $topCarreras
            ]
        ]);
    }
}
