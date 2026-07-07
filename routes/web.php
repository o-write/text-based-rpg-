<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

/*
|--------------------------------------------------------------------------
| Web Routes — Text-Based RPG Game
|--------------------------------------------------------------------------
*/

// Show the main game screen
Route::get('/', [GameController::class, 'index'])->name('game.index');

// Player attacks the enemy
Route::post('/attack', [GameController::class, 'attack'])->name('game.attack');

// Player uses the Get Motivation heal action (fetches quote from external API)
Route::post('/motivation', [GameController::class, 'getMotivation'])->name('game.motivation');

// Reset the game session back to defaults
Route::post('/reset', [GameController::class, 'reset'])->name('game.reset');
