<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/', 'home')->name('home');
Route::livewire('/login', 'auth.login')->name('login');

Route::prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        Route::livewire('/home', 'dashboard.home')->name('home');
        Route::livewire('/game/genshin/{uid}', 'dashboard.game.genshin')->name('game.genshin');
        Route::livewire('/game/genshin/{uid}/characters/{character}/detail/{id}', 'dashboard.game.genshin.characters')->name('game.genshin.characters.detail');
        Route::livewire('/game/startrail/{uid}', 'dashboard.game.startrail')->name('game.startrail');
        Route::livewire('/game/honkai/{uid}', 'dashboard.game.honkai')->name('game.honkai');
        Route::livewire('/game/zzz/{uid}', 'dashboard.game.zzz')->name('game.zzz');
        // Add more dashboard sub-routes here

    });

