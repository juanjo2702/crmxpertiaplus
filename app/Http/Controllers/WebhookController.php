<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Contact;
use App\Models\Message;
use App\Services\WhatsAppService;

class WebhookController extends Controller
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    /**
     * Verify the webhook (GET request)
     */
    public function verify(Request $request)
    {
        $verifyToken = env('META_VERIFY_TOKEN', 'happy_cat_unitepc');

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === $verifyToken) {
                Log::info('Webhook verified successfully.');
                return response($challenge, 200);
            }
        }

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming events (POST request)
     */
    public function handle(Request $request)
    {
        $body = $request->all();
        Log::info('Webhook received:', $body);

        // Check if it's a WhatsApp status update or message
        if (isset($body['entry'][0]['changes'][0]['value']['messages'])) {
            $messageData = $body['entry'][0]['changes'][0]['value']['messages'][0];
            $contactData = $body['entry'][0]['changes'][0]['value']['contacts'][0] ?? null;

            $this->processMessage($messageData, $contactData);
        }

        return response('EVENT_RECEIVED', 200);
    }

    protected function processMessage($msgData, $contactData)
    {
        $waId = $msgData['from']; // The sender's phone number
        $wamId = $msgData['id'];

        // Find or create Contact
        $contact = Contact::firstOrCreate(
            ['wa_id' => $waId],
            [
                'name' => $contactData['profile']['name'] ?? 'Unknown',
            ]
        );

        $body = '';
        $type = $msgData['type'];
        $metadata = $msgData;

        if ($type === 'text') {
            $body = $msgData['text']['body'];
        } elseif ($type === 'image') {
            // Download and save the image
            $mediaId = $msgData['image']['id'] ?? null;
            $caption = $msgData['image']['caption'] ?? '';

            if ($mediaId) {
                $mediaInfo = $this->whatsapp->downloadMedia($mediaId);
                if ($mediaInfo) {
                    $body = $mediaInfo['url'];
                    $metadata['local_path'] = $mediaInfo['path'];
                    $metadata['mime_type'] = $mediaInfo['mime_type'];
                } else {
                    $body = "[Imagen no disponible]";
                }
            }

            if ($caption) {
                $metadata['caption'] = $caption;
            }
        } elseif (in_array($type, ['video', 'audio', 'document', 'sticker'])) {
            // Handle other media types similarly
            $mediaId = $msgData[$type]['id'] ?? null;
            $filename = $msgData[$type]['filename'] ?? null;

            if ($mediaId) {
                $mediaInfo = $this->whatsapp->downloadMedia($mediaId);
                if ($mediaInfo) {
                    $body = $filename ?: $mediaInfo['url'];
                    $metadata['local_path'] = $mediaInfo['path'];
                    $metadata['local_url'] = $mediaInfo['url'];
                    $metadata['mime_type'] = $mediaInfo['mime_type'];
                    $metadata['filename'] = $filename;
                } else {
                    $body = $filename ?: "[Media: $type]";
                }
            }
        } else {
            $body = "[Media: $type]";
        }

        // Save Message
        $newMessage = Message::create([
            'wam_id' => $wamId,
            'contact_id' => $contact->id,
            'type' => $type,
            'body' => $body,
            'status' => 'delivered',
            'direction' => 'incoming',
            'metadata' => json_encode($metadata),
        ]);

        broadcast(new \App\Events\MessageReceived($newMessage));
    }
}
