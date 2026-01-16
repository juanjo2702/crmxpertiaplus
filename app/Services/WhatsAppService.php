<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsAppService
{
    protected $baseUrl = 'https://graph.facebook.com/v21.0';
    protected $token;
    protected $phoneId;
    protected $wabaId;

    public function __construct()
    {
        $this->token = env('META_ACCESS_TOKEN');
        $this->phoneId = env('META_PHONE_ID');
        $this->wabaId = env('META_WABA_ID');
    }

    /**
     * Get all templates (Debug)
     */
    public function getTemplates()
    {
        $url = "{$this->baseUrl}/{$this->wabaId}/message_templates";
        return $this->sendRequest($url, [], 'get');
    }

    /**
     * Send a template message (Dynamic)
     */
    public function sendTemplateMessage($to, $templateName, $languageCode = 'es', $components = [])
    {
        $url = "{$this->baseUrl}/{$this->phoneId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode
                ]
            ]
        ];

        if (!empty($components)) {
            $payload['template']['components'] = $components;
        }

        Log::info('Sending WhatsApp Template', ['payload' => $payload]);

        return $this->sendRequest($url, $payload);
    }

    /**
     * Send a template message (Hello World)
     * @deprecated Use sendTemplateMessage instead
     */
    public function sendHelloTemplate($to)
    {
        return $this->sendTemplateMessage($to, 'hello_world', 'en_US');
    }

    /**
     * Send a generic text message
     */
    public function sendTextMessage($to, $message)
    {
        $url = "{$this->baseUrl}/{$this->phoneId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ];

        return $this->sendRequest($url, $payload);
    }

    /**
     * Send an image message
     */
    public function sendImageMessage($to, $mediaId, $caption = null)
    {
        $url = "{$this->baseUrl}/{$this->phoneId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'image',
            'image' => [
                'id' => $mediaId
            ]
        ];

        if ($caption) {
            $payload['image']['caption'] = $caption;
        }

        return $this->sendRequest($url, $payload);
    }

    /**
     * Send a document message
     */
    public function sendDocumentMessage($to, $mediaId, $filename = null)
    {
        $url = "{$this->baseUrl}/{$this->phoneId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'document',
            'document' => [
                'id' => $mediaId
            ]
        ];

        if ($filename) {
            $payload['document']['filename'] = $filename;
        }

        return $this->sendRequest($url, $payload);
    }

    /**
     * Send an audio message
     */
    public function sendAudioMessage($to, $mediaId)
    {
        $url = "{$this->baseUrl}/{$this->phoneId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'audio',
            'audio' => [
                'id' => $mediaId
            ]
        ];

        return $this->sendRequest($url, $payload);
    }

    /**
     * Upload media to WhatsApp (returns media_id)
     */
    public function uploadMedia($filePath, $mimeType)
    {
        $url = "{$this->baseUrl}/{$this->phoneId}/media";

        try {
            // Get file contents and name
            $fileContents = file_get_contents($filePath);
            $fileName = basename($filePath);

            // Ensure MIME type is not empty
            if (empty($mimeType) || $mimeType === 'application/octet-stream') {
                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
            }

            $response = Http::withToken($this->token)
                ->attach('file', $fileContents, $fileName, ['Content-Type' => $mimeType])
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'type' => $mimeType
                ]);

            if ($response->failed()) {
                Log::error('WhatsApp Upload Error', ['body' => $response->body()]);
                return null;
            }

            return $response->json()['id'] ?? null;
        } catch (\Exception $e) {
            Log::error('WhatsApp Upload Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Download media from WhatsApp and save locally
     */
    public function downloadMedia($mediaId)
    {
        try {
            // Step 1: Get media URL
            $urlResponse = Http::withToken($this->token)
                ->get("{$this->baseUrl}/{$mediaId}");

            if ($urlResponse->failed()) {
                Log::error('Failed to get media URL', ['body' => $urlResponse->body()]);
                return null;
            }

            $mediaUrl = $urlResponse->json()['url'] ?? null;
            $mimeType = $urlResponse->json()['mime_type'] ?? 'image/jpeg';

            if (!$mediaUrl) {
                return null;
            }

            // Step 2: Download the file
            $fileResponse = Http::withToken($this->token)->get($mediaUrl);

            if ($fileResponse->failed()) {
                Log::error('Failed to download media', ['status' => $fileResponse->status()]);
                return null;
            }

            // Step 3: Save locally
            $extension = $this->getExtensionFromMime($mimeType);
            $filename = $mediaId . '.' . $extension;
            $path = 'chat-media/' . $filename;

            Storage::disk('public')->put($path, $fileResponse->body());

            return [
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
                'mime_type' => $mimeType
            ];
        } catch (\Exception $e) {
            Log::error('Download Media Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    protected function getExtensionFromMime($mimeType)
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'video/mp4' => 'mp4',
            'audio/ogg' => 'ogg',
            'audio/mpeg' => 'mp3',
            'application/pdf' => 'pdf',
        ];
        return $map[$mimeType] ?? 'bin';
    }

    protected function sendRequest($url, $payload, $method = 'post')
    {
        try {
            $http = Http::withToken($this->token);

            if ($method === 'post') {
                $response = $http->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $payload);
            } else {
                $response = $http->get($url, $payload);
            }

            if ($response->failed()) {
                Log::error('WhatsApp API Error', [
                    'body' => $response->body(),
                    'status' => $response->status()
                ]);
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('WhatsApp Exception', ['message' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }
}
