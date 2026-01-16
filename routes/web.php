<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


use App\Services\WhatsAppService;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/test-whatsapp', function (WhatsAppService $whatsapp) {
    $phone = request('phone');
    if (!$phone) {
        return response()->json(['error' => 'Please provide a phone number: /test-whatsapp?phone=591XXXXXXXX'], 400);
    }

    $template = request('template', 'hello_world'); // Default to hello_world if not provided
    $lang = request('lang', 'es'); // Default to es

    $response = $whatsapp->sendTemplateMessage($phone, $template, $lang);
    return response()->json($response);
});

Route::get('/debug-templates', function (WhatsAppService $whatsapp) {
    return response()->json($whatsapp->getTemplates());
});


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/chat', [ChatController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('chat');

Route::get('/chat/contacts', [ChatController::class, 'contacts'])
    ->middleware(['auth', 'verified'])
    ->name('chat.contacts');

Route::get('/chat/{contact}/messages', [ChatController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('chat.messages');

Route::post('/chat/{contact}/messages', [ChatController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('chat.send');

Route::post('/chat/{contact}/read', [ChatController::class, 'markAsRead'])
    ->middleware(['auth', 'verified'])
    ->name('chat.read');

Route::post('/chat/{contact}/image', [ChatController::class, 'sendImage'])
    ->middleware(['auth', 'verified'])
    ->name('chat.sendImage');

Route::post('/chat/{contact}/document', [ChatController::class, 'sendDocument'])
    ->middleware(['auth', 'verified'])
    ->name('chat.sendDocument');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
