<?php

use App\Http\Controllers\UpdateTitlesController;
use Illuminate\Support\Facades\Route;

// Update Titles Route
Route::get('/update-titles', [UpdateTitlesController::class, 'updateTitles']);
