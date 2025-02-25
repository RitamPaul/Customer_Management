<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;

// User
Route::get('/', function() {return view('User/register');});
Route::post('/save-add-user', [userController::class, 'saveAddUser'])->name('saveAddUser');

Route::get('/login-user', function() {return view('User/login');})->name('loginUser');
Route::post('/verify-user', [userController::class, 'verifyUser'])->name('verifyUser');

// Route::get('/dashboard-user', function() {return view('User/customer-dashboard');})->name('dashboardUser');
Route::get('/dashboard-user', [userController::class, 'dashboardUser'])->name('dashboardUser');

Route::get('/add-appointment', function() {return view('User/add-appointment');})->name('againAddAppointment');
Route::post('/add-appointment', [userController::class, 'addAppointment'])->name('addAppointment');
Route::post('/save-appointment', [userController::class, 'saveAppointment'])->name('saveAppointment');

Route::get('/view-appointment/{id}', [userController::class, 'viewAppointment'])->name('viewAppointment');

Route::get('/edit-appointment/{id}', [userController::class, 'editAppointment'])->name('editAppointment');
Route::put('/update-appointment/{id}', [userController::class, 'updateAppointment'])->name('updateAppointment');

Route::get('/delete-appointment/{id}', [userController::class, 'deleteAppointment'])->name('deleteAppointment');

Route::get('/user-logout', [userController::class, 'userLogout'])->name('userLogout');

// Admin