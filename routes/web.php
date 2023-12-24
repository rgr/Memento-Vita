<?php

use App\Http\Controllers\MementoVitaController;
use Illuminate\Support\Facades\Route;

Route::get('/{name?}', [MementoVitaController::class, 'index'])->where('name', '[A-Za-z]+')->name('index');
