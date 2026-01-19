<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

use App\Models\TenantSede;
use App\Models\TenantCarrera;
use App\Models\TenantOferta;
use App\Models\TenantEvento;

class ChatController extends Controller
{
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;

        $sedes = TenantSede::where('tenant_id', $tenantId)->get();
        $carreras = TenantCarrera::where('tenant_id', $tenantId)->get();
        $ofertas = TenantOferta::where('tenant_id', $tenantId)->where('activo', true)->get();
        $eventos = TenantEvento::where('tenant_id', $tenantId)->where('activo', true)->get();

        return Inertia::render('Chat/Index', [
            'initialContacts' => $this->getContactsData(),
            'catalogs' => [
                'sedes' => $sedes,
                'carreras' => $carreras,
                'ofertas' => $ofertas,
                'eventos' => $eventos
            ]
        ]);
    }

    public function contacts()
    {
        return response()->json($this->getContactsData());
    }

    private function getContactsData()
    {
        $user = Auth::user();
        $tenantId = $user?->tenant_id;

        $query = Contact::with(['messages' => function ($query) {
            $query->orderBy('created_at', 'desc')->take(1);
        }]);

        // Filter by tenant if user has one (multi-tenant isolation)
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query
            ->withCount(['messages as unread_count' => function ($query) {
                $query->where('direction', 'incoming')
                    ->whereNull('read_at');
            }])
            ->orderByDesc(
                \App\Models\Message::select('created_at')
                    ->whereColumn('contact_id', 'contacts.id')
                    ->orderByDesc('created_at')
                    ->limit(1)
            )
            ->get()
            ->map(function ($contact) {
                $lastMsg = $contact->messages->first();

                // Format lastMessage based on type
                $lastMessageText = '';
                if ($lastMsg) {
                    switch ($lastMsg->type) {
                        case 'image':
                            $lastMessageText = '📷 Imagen';
                            break;
                        case 'video':
                            $lastMessageText = '🎥 Video';
                            break;
                        case 'audio':
                            $lastMessageText = '🎵 Audio';
                            break;
                        case 'document':
                            $lastMessageText = '📄 Documento';
                            break;
                        case 'sticker':
                            $lastMessageText = '🎨 Sticker';
                            break;
                        default:
                            $lastMessageText = $lastMsg->body;
                    }
                }

                return [
                    'id' => $contact->id,
                    'name' => $contact->name ?? $contact->wa_id,
                    'avatar' => $contact->profile_pic,
                    'time' => $lastMsg ? $lastMsg->created_at->toIso8601String() : '',
                    'lastMessage' => $lastMessageText,
                    'unread' => $contact->unread_count,
                ];
            });
    }

    public function show(Contact $contact)
    {
        // Return messages for a specific contact
        return response()->json(
            $contact->messages()
                ->orderBy('created_at', 'asc')
                ->get()
        );
    }

    /**
     * Mark all incoming messages from a contact as read
     */
    public function markAsRead(Contact $contact)
    {
        $updated = $contact->messages()
            ->where('direction', 'incoming')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['marked' => $updated]);
    }

    /**
     * Get available templates
     */
    public function getTemplates(\App\Services\WhatsAppService $whatsapp)
    {
        return response()->json($whatsapp->getTemplates());
    }

    /**
     * Initiate a new chat or get existing one by phone
     */
    public function initiate(Request $request, \App\Services\WhatsAppService $whatsapp)
    {
        $request->validate([
            'phone' => 'required|string',
            'template' => 'nullable|string'
        ]);

        // Clean phone number (remove +, spaces, etc)
        $phone = preg_replace('/[^0-9]/', '', $request->phone);

        // Find or create contact for this tenant
        $user = Auth::user();
        $tenantId = $user?->tenant_id;

        $contact = Contact::firstOrCreate(
            ['wa_id' => $phone, 'tenant_id' => $tenantId],
            ['name' => $request->input('name', $phone)]
        );

        // If template provided, send it
        if ($request->filled('template')) {
            try {
                $this->configureWhatsApp($whatsapp);
                $whatsapp->sendTemplateMessage($contact->wa_id, $request->template, 'es');

                // Log system message
                Message::create([
                    'wam_id' => 'system_' . uniqid(),
                    'contact_id' => $contact->id,
                    'type' => 'template',
                    'body' => "Inicio de conversación (Plantilla: {$request->template})",
                    'direction' => 'outgoing',
                    'status' => 'sent'
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send template: ' . $e->getMessage());
            }
        }

        // Return structured like getContactsData
        return response()->json([
            'id' => $contact->id,
            'name' => $contact->name ?? $contact->wa_id,
            'avatar' => $contact->profile_pic,
            'time' => $contact->updated_at,
            'lastMessage' => $request->filled('template') ? 'Plantilla enviada' : '',
            'unread' => 0,
        ]);
    }

    public function store(Request $request, Contact $contact, \App\Services\WhatsAppService $whatsapp)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $text = $request->input('message');

        // Configure WhatsApp with Tenant credentials
        $this->configureWhatsApp($whatsapp);

        // 1. Send via WhatsApp API
        $response = $whatsapp->sendTextMessage($contact->wa_id, $text);

        if (isset($response['error'])) {
            Log::error('WhatsApp Send Error', ['response' => $response]);
            return response()->json(['error' => 'Failed to send message via Meta', 'details' => $response], 500);
        }

        $wamId = $response['messages'][0]['id'] ?? 'temp_' . uniqid();

        // 2. Save to DB
        $message = Message::create([
            'wam_id' => $wamId,
            'contact_id' => $contact->id,
            'type' => 'text',
            'body' => $text,
            'status' => 'sent',
            'direction' => 'outgoing',
            'metadata' => json_encode($response),
        ]);

        return response()->json($message);
    }

    /**
     * Send an image message
     */
    public function sendImage(Request $request, Contact $contact, \App\Services\WhatsAppService $whatsapp)
    {
        try {
            $request->validate([
                'image' => 'required|image|max:5120', // 5MB max
                'caption' => 'nullable|string|max:1024'
            ]);

            $file = $request->file('image');
            $caption = $request->input('caption', '');

            // Store locally first
            $path = $file->store('chat-media', 'public');
            $localUrl = Storage::disk('public')->url($path);

            // Try to upload to WhatsApp (may fail if token invalid)
            $mediaId = null;
            $wamId = 'local_' . uniqid();

            try {
                $this->configureWhatsApp($whatsapp);
                $mediaId = $whatsapp->uploadMedia($file->getRealPath(), $file->getClientMimeType());

                if ($mediaId) {
                    $response = $whatsapp->sendImageMessage($contact->wa_id, $mediaId, $caption);
                    $wamId = $response['messages'][0]['id'] ?? $wamId;
                }
            } catch (\Exception $e) {
                Log::warning('WhatsApp image upload failed: ' . $e->getMessage());
            }

            // Save to DB (even if WhatsApp failed)
            $message = Message::create([
                'wam_id' => $wamId,
                'contact_id' => $contact->id,
                'type' => 'image',
                'body' => $localUrl,
                'status' => $mediaId ? 'sent' : 'pending',
                'direction' => 'outgoing',
                'metadata' => json_encode([
                    'caption' => $caption,
                    'local_path' => $path,
                    'media_id' => $mediaId
                ]),
            ]);

            return response()->json($message);
        } catch (\Exception $e) {
            Log::error('sendImage error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Send a document
     */
    public function sendDocument(Request $request, Contact $contact, \App\Services\WhatsAppService $whatsapp)
    {
        try {
            $request->validate([
                'document' => 'required|file|max:16384', // 16MB max
                'filename' => 'nullable|string'
            ]);

            $file = $request->file('document');
            $filename = $request->input('filename', $file->getClientOriginalName());

            // Store locally
            $path = $file->store('chat-media', 'public');
            $localUrl = Storage::disk('public')->url($path);

            // Try to upload to WhatsApp (may fail if token invalid)
            $mediaId = null;
            $wamId = 'local_' . uniqid();

            try {
                $this->configureWhatsApp($whatsapp);
                $mediaId = $whatsapp->uploadMedia($file->getRealPath(), $file->getClientMimeType());

                if ($mediaId) {
                    $response = $whatsapp->sendDocumentMessage($contact->wa_id, $mediaId, $filename);
                    $wamId = $response['messages'][0]['id'] ?? $wamId;
                }
            } catch (\Exception $e) {
                Log::warning('WhatsApp upload failed, saving locally only: ' . $e->getMessage());
            }

            // Save to DB (even if WhatsApp failed)
            $message = Message::create([
                'wam_id' => $wamId,
                'contact_id' => $contact->id,
                'type' => 'document',
                'body' => $filename,
                'status' => $mediaId ? 'sent' : 'pending',
                'direction' => 'outgoing',
                'metadata' => json_encode([
                    'local_path' => $path,
                    'local_url' => $localUrl,
                    'filename' => $filename
                ]),
            ]);

            return response()->json($message);
        } catch (\Exception $e) {
            Log::error('sendDocument error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Send an audio message
     */
    public function sendAudio(Request $request, Contact $contact, \App\Services\WhatsAppService $whatsapp)
    {
        try {
            $request->validate([
                'audio' => 'required|file|max:16384', // 16MB max
            ]);

            $file = $request->file('audio');

            // Store locally
            $path = $file->store('chat-media', 'public');
            $localUrl = Storage::disk('public')->url($path);

            // Try to upload to WhatsApp
            $mediaId = null;
            $wamId = 'local_' . uniqid();

            try {
                $this->configureWhatsApp($whatsapp);
                $mediaId = $whatsapp->uploadMedia($file->getRealPath(), $file->getClientMimeType());

                if ($mediaId) {
                    $response = $whatsapp->sendAudioMessage($contact->wa_id, $mediaId);
                    $wamId = $response['messages'][0]['id'] ?? $wamId;
                }
            } catch (\Exception $e) {
                Log::warning('WhatsApp audio upload failed: ' . $e->getMessage());
            }

            // Save to DB
            $message = Message::create([
                'wam_id' => $wamId,
                'contact_id' => $contact->id,
                'type' => 'audio',
                'body' => $localUrl, // We store the URL for playback
                'status' => $mediaId ? 'sent' : 'pending',
                'direction' => 'outgoing',
                'metadata' => json_encode([
                    'local_path' => $path,
                    'media_id' => $mediaId
                ]),
            ]);

            return response()->json($message);
        } catch (\Exception $e) {
            Log::error('sendAudio error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Get detailed CRM info for a contact
     */
    public function contactDetails(Contact $contact)
    {
        // Security check
        if ($contact->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized');
        }

        $contact->load(['carreras', 'sedes', 'ofertas', 'eventos']);

        return response()->json([
            'id' => $contact->id,
            'name' => $contact->name,
            'wa_id' => $contact->wa_id,
            'email' => $contact->email,
            'nivel_interes' => $contact->nivel_interes,
            'estado_crm' => $contact->estado_crm,
            'notas_crm' => $contact->notas_crm,
            'carreras' => $contact->carreras->pluck('id'),
            'sedes' => $contact->sedes->pluck('id'),
            'ofertas' => $contact->ofertas->pluck('id'),
            'eventos' => $contact->eventos->pluck('id'),
        ]);
    }

    /**
     * Update CRM info for a contact
     */
    public function updateContact(Request $request, Contact $contact)
    {
        if ($contact->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'nivel_interes' => 'nullable|string',
            'estado_crm' => 'nullable|string',
            'notas_crm' => 'nullable|string',
            'sedes' => 'nullable|array',
            'carreras' => 'nullable|array',
            'ofertas' => 'nullable|array',
            'eventos' => 'nullable|array',
        ]);

        $contact->update([
            'nivel_interes' => $data['nivel_interes'] ?? null,
            'estado_crm' => $data['estado_crm'] ?? null,
            'notas_crm' => $data['notas_crm'] ?? null,
        ]);

        if (isset($data['sedes'])) $contact->sedes()->sync($data['sedes']);
        if (isset($data['carreras'])) $contact->carreras()->sync($data['carreras']);
        if (isset($data['ofertas'])) $contact->ofertas()->sync($data['ofertas']);
        if (isset($data['eventos'])) $contact->eventos()->sync($data['eventos']);

        return response()->json(['success' => true]);
    }
    /**
     * Configure WhatsApp service with tenant credentials
     */
    private function configureWhatsApp(\App\Services\WhatsAppService $whatsapp)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            // Fallback for superadmin or testing if needed, or throw error
            // For now, require tenant
            throw new \Exception('Usuario no asociado a una empresa.');
        }

        // Check if credentials exist
        if (!empty($tenant->whatsapp_token) && !empty($tenant->whatsapp_phone_id)) {
            $whatsapp->setCredentials(
                $tenant->whatsapp_token,
                $tenant->whatsapp_phone_id,
                $tenant->whatsapp_business_account_id
            );
        } else {
            // Fallback to .env (Default behavior of Service)
            Log::warning("Tenant {$tenant->id} has no WhatsApp credentials configured. Using system defaults from .env");
        }
    }
}
