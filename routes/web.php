<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;

// Guest Front-End
Route::get('/', [HotelController::class, 'index'])->name('index');

// Admin Dashboard
Route::get('/admin', [HotelController::class, 'admin'])->name('admin');

// Configuration Settings
Route::post('/admin/settings', [HotelController::class, 'updateSettings'])->name('admin.settings.update');

// Room Type CRUD
Route::post('/admin/room-type', [HotelController::class, 'storeRoomType'])->name('admin.room-type.store');
Route::post('/admin/room-type/delete/{id}', [HotelController::class, 'deleteRoomType'])->name('admin.room-type.delete');

// Physical Room CRUD
Route::post('/admin/room', [HotelController::class, 'storeRoom'])->name('admin.room.store');
Route::post('/admin/room/delete/{id}', [HotelController::class, 'deleteRoom'])->name('admin.room.delete');

// Dynamic Pricing CRUD
Route::post('/admin/pricing', [HotelController::class, 'storePricingRule'])->name('admin.pricing.store');
Route::post('/admin/pricing/delete/{id}', [HotelController::class, 'deletePricingRule'])->name('admin.pricing.delete');

// Reservations / Blockings CRUD & Room Change
Route::post('/admin/booking', [HotelController::class, 'storeBooking'])->name('admin.booking.store');
Route::post('/admin/booking/delete/{id}', [HotelController::class, 'deleteBooking'])->name('admin.booking.delete');
Route::get('/admin/booking/available-rooms', [HotelController::class, 'getAvailableRoomsForEdit'])->name('admin.booking.available-rooms');
Route::post('/admin/booking/change-room', [HotelController::class, 'changeBookingRoom'])->name('admin.booking.change-room');
Route::post('/admin/booking/checkout/{id}', [HotelController::class, 'checkoutBooking'])->name('admin.booking.checkout');
Route::get('/admin/rooms/availability-grid', [HotelController::class, 'getRoomAvailabilityGrid'])->name('admin.rooms.availability-grid');
