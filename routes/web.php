<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\PhraseController;
use App\Http\Middleware\LanguageMiddleware;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\SourcePhraseController;

Route::get('/', function() {
    return view('dashboard.index');
})->name('home');

Route::middleware('guest')->group(function() {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
});

Route::get('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('translations')->as('translations.')->middleware('auth')->group(function() {
    Route::get('/', [TranslationController::class, 'index'])->name('index');
    Route::post('/list', [TranslationController::class, 'list'])->name('list');
    Route::post('/store', [TranslationController::class, 'store'])->name('store');
    Route::post('/auto-translate/{id}', [TranslationController::class, 'autoTranslation'])->name('auto-translation');
    Route::delete('/destroy/{id}', [TranslationController::class, 'destroy'])->name('destroy');

    Route::prefix('phrases')->as('phrases.')->group(function() {
        Route::get('/{id}', [PhraseController::class, 'index'])->name('index');
        Route::post('/list/{id}', [PhraseController::class, 'list'])->name('list');

        Route::post('/update/{id}', [PhraseController::class, 'update'])->name('update');
        Route::post('/translate/{id}', [PhraseController::class, 'translate'])->name('translate');
    });

    Route::prefix('source-translation')->as('source-translation.')->group(function() {
        Route::get('/', [SourcePhraseController::class, 'index'])->name('index');
        Route::post('/list', [SourcePhraseController::class, 'list'])->name('list');

        Route::post('/store', [SourcePhraseController::class, 'store'])->name('store');
    });
    Route::post('/public', [TranslationController::class, 'public'])->name('public');
});

Route::get('/switch-locale', function (Illuminate\Http\Request $request) {
    $locale = $request->query('locale', 'en');
    Session::forget('locale');

    session()->put('locale', $locale);

    return redirect()->back()->with('success', 'Locale switched successfully.');
})->name('locale.switch')->middleware([LanguageMiddleware::class]);