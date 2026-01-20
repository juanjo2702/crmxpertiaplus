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

Route::post('/chat/{contact}/audio', [ChatController::class, 'sendAudio'])
    ->middleware(['auth', 'verified'])
    ->name('chat.sendAudio');

Route::get('/chat/contact/{contact}', [ChatController::class, 'contactDetails'])
    ->middleware(['auth', 'verified'])
    ->name('chat.contact.details');

Route::put('/chat/contact/{contact}', [ChatController::class, 'updateContact'])
    ->middleware(['auth', 'verified'])
    ->name('chat.contact.update');

// Chat Assignment & Transfer Routes
Route::post('/chat/{contact}/transfer', [ChatController::class, 'transferChat'])
    ->middleware(['auth', 'verified'])
    ->name('chat.transfer');

Route::get('/chat/transfers', [ChatController::class, 'getTransferHistory'])
    ->middleware(['auth', 'verified'])
    ->name('chat.transfers');

Route::get('/chat/notifications', [ChatController::class, 'getUserNotifications'])
    ->middleware(['auth', 'verified'])
    ->name('chat.notifications');

Route::post('/chat/transfer/{transfer}/seen', [ChatController::class, 'markTransferSeen'])
    ->middleware(['auth', 'verified'])
    ->name('chat.transfer.seen');

Route::get('/chat/team', [ChatController::class, 'getTeamMembers'])
    ->middleware(['auth', 'verified'])
    ->name('chat.team');

Route::get('/chat/filter', [ChatController::class, 'filterContacts'])
    ->middleware(['auth', 'verified'])
    ->name('chat.filter');

// Quick Replies Routes
Route::get('/quick-replies', [\App\Http\Controllers\QuickReplyController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('quick-replies.index');

Route::get('/quick-replies/manage', [\App\Http\Controllers\QuickReplyController::class, 'manage'])
    ->middleware(['auth', 'verified'])
    ->name('quick-replies.manage');

Route::post('/quick-replies/categories', [\App\Http\Controllers\QuickReplyController::class, 'storeCategory'])
    ->middleware(['auth', 'verified'])
    ->name('quick-replies.categories.store');

Route::put('/quick-replies/categories/{category}', [\App\Http\Controllers\QuickReplyController::class, 'updateCategory'])
    ->middleware(['auth', 'verified'])
    ->name('quick-replies.categories.update');

Route::delete('/quick-replies/categories/{category}', [\App\Http\Controllers\QuickReplyController::class, 'destroyCategory'])
    ->middleware(['auth', 'verified'])
    ->name('quick-replies.categories.destroy');

Route::post('/quick-replies/categories/{category}/toggle', [\App\Http\Controllers\QuickReplyController::class, 'toggleCategory'])
    ->middleware(['auth', 'verified'])
    ->name('quick-replies.categories.toggle');

Route::post('/quick-replies/categories/reorder', [\App\Http\Controllers\QuickReplyController::class, 'reorderCategories'])
    ->middleware(['auth', 'verified'])
    ->name('quick-replies.categories.reorder');

Route::post('/quick-replies', [\App\Http\Controllers\QuickReplyController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('quick-replies.store');

Route::post('/quick-replies/{quickReply}', [\App\Http\Controllers\QuickReplyController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('quick-replies.update');

Route::delete('/quick-replies/{quickReply}', [\App\Http\Controllers\QuickReplyController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('quick-replies.destroy');

Route::post('/quick-replies/{quickReply}/toggle', [\App\Http\Controllers\QuickReplyController::class, 'toggle'])
    ->middleware(['auth', 'verified'])
    ->name('quick-replies.toggle');

Route::post('/quick-replies/reorder', [\App\Http\Controllers\QuickReplyController::class, 'reorderReplies'])
    ->middleware(['auth', 'verified'])
    ->name('quick-replies.reorder');

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
    Route::post('/configuracion', [\App\Http\Controllers\TenantAdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/password', [\App\Http\Controllers\TenantAdminController::class, 'updatePassword'])->name('password.update');

    // Catalogs
    // Sedes
    Route::get('/sedes', [\App\Http\Controllers\TenantCatalogController::class, 'sedes'])->name('sedes');
    Route::post('/sedes', [\App\Http\Controllers\TenantCatalogController::class, 'storeSede'])->name('sedes.store');
    Route::put('/sedes/{sede}', [\App\Http\Controllers\TenantCatalogController::class, 'updateSede'])->name('sedes.update');
    Route::delete('/sedes/{sede}', [\App\Http\Controllers\TenantCatalogController::class, 'destroySede'])->name('sedes.destroy');

    // Reportes
    Route::get('/reportes', [\App\Http\Controllers\TenantReportController::class, 'index'])->name('reports');

    // Chat docs
    Route::post('/chat/send-document/{contact}', [\App\Http\Controllers\ChatController::class, 'sendDocument'])->name('chat.sendDocument');

    // Carreras
    Route::get('/carreras', [\App\Http\Controllers\TenantCatalogController::class, 'carreras'])->name('carreras');
    Route::post('/carreras', [\App\Http\Controllers\TenantCatalogController::class, 'storeCarrera'])->name('carreras.store');
    Route::put('/carreras/{carrera}', [\App\Http\Controllers\TenantCatalogController::class, 'updateCarrera'])->name('carreras.update');
    Route::delete('/carreras/{carrera}', [\App\Http\Controllers\TenantCatalogController::class, 'destroyCarrera'])->name('carreras.destroy');

    // Ofertas
    Route::get('/ofertas', [\App\Http\Controllers\TenantCatalogController::class, 'ofertas'])->name('ofertas');
    Route::post('/ofertas', [\App\Http\Controllers\TenantCatalogController::class, 'storeOferta'])->name('ofertas.store');
    Route::put('/ofertas/{oferta}', [\App\Http\Controllers\TenantCatalogController::class, 'updateOferta'])->name('ofertas.update');
    Route::delete('/ofertas/{oferta}', [\App\Http\Controllers\TenantCatalogController::class, 'destroyOferta'])->name('ofertas.destroy');

    // Eventos
    Route::get('/eventos', [\App\Http\Controllers\TenantCatalogController::class, 'eventos'])->name('eventos');
    Route::post('/eventos', [\App\Http\Controllers\TenantCatalogController::class, 'storeEvento'])->name('eventos.store');
    Route::put('/eventos/{evento}', [\App\Http\Controllers\TenantCatalogController::class, 'updateEvento'])->name('eventos.update');
    Route::delete('/eventos/{evento}', [\App\Http\Controllers\TenantCatalogController::class, 'destroyEvento'])->name('eventos.destroy');
});

require __DIR__ . '/auth.php';
