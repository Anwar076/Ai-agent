<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Main chat interface
Route::get('/', [ChatController::class, 'index'])->name('chat.index');

// Chat API routes
Route::prefix('api/chat')->name('chat.')->group(function () {
    Route::post('/start', [ChatController::class, 'startConversation'])->name('start');
    Route::post('/message', [ChatController::class, 'sendMessage'])->name('message');
    Route::get('/conversation/{conversation_id}', [ChatController::class, 'getConversation'])->name('conversation');
    Route::post('/typing', [ChatController::class, 'typing'])->name('typing');
    Route::post('/end', [ChatController::class, 'endConversation'])->name('end');
});

// Quote management
Route::prefix('quotes')->name('quotes.')->group(function () {
    Route::get('/', [QuoteController::class, 'index'])->name('index');
    Route::get('/{quote}', [QuoteController::class, 'show'])->name('show');
    Route::get('/{quote}/pdf', [QuoteController::class, 'downloadPdf'])->name('pdf');
    Route::post('/{quote}/send', [QuoteController::class, 'sendEmail'])->name('send');
});

// Incident management
Route::prefix('incidents')->name('incidents.')->group(function () {
    Route::get('/', [IncidentController::class, 'index'])->name('index');
    Route::get('/{incident}', [IncidentController::class, 'show'])->name('show');
    Route::patch('/{incident}/resolve', [IncidentController::class, 'resolve'])->name('resolve');
});

// Admin dashboard (protected routes - add authentication middleware later)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/conversations', [AdminController::class, 'conversations'])->name('conversations');
    Route::get('/quotes', [AdminController::class, 'quotes'])->name('quotes');
    Route::get('/incidents', [AdminController::class, 'incidents'])->name('incidents');
    Route::get('/conversations/{conversation}', [AdminController::class, 'showConversation'])->name('conversations.show');
});

require __DIR__.'/auth.php';
