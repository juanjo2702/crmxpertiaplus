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

Route::post('/chat/initiate', [ChatController::class, 'initiate'])
    ->middleware(['auth', 'verified'])
    ->name('chat.initiate');

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


// Super Admin Routes
Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureUserIsSuperAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('dashboard');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\SuperAdminController::class, 'profile'])->name('profile');
    Route::patch('/profile', [\App\Http\Controllers\SuperAdminController::class, 'updateProfile'])->name('profile.update');
    Route::post('/password', [\App\Http\Controllers\SuperAdminController::class, 'updatePassword'])->name('password.update');

    // Tenants CRUD
    Route::resource('tenants', \App\Http\Controllers\TenantController::class);
});

// Tenant Admin Routes
Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureUserIsTenantAdmin::class])->prefix('empresa')->name('tenant.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\TenantAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/usuarios', [\App\Http\Controllers\TenantAdminController::class, 'users'])->name('users');
    Route::post('/usuarios', [\App\Http\Controllers\TenantAdminController::class, 'storeUser'])->name('users.store');
    Route::delete('/usuarios/{user}', [\App\Http\Controllers\TenantAdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/configuracion', [\App\Http\Controllers\TenantAdminController::class, 'settings'])->name('settings');
    Route::post('/password', [\App\Http\Controllers\TenantAdminController::class, 'updatePassword'])->name('password.update');
});

require __DIR__ . '/auth.php';
