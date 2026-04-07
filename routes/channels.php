<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Sejajan Orders - Buyer channel (user yang order)
Broadcast::channel('orders.buyer.{userId}', function ($user, $userId) {
    return (string) $user->id === (string) $userId;
});

// Sejajan Orders - Seller channel (owner toko)
Broadcast::channel('orders.seller.{userId}', function ($user, $userId) {
    return (string) $user->id === (string) $userId;
});

// LMS - Notifikasi materi ke kelas
Broadcast::channel('classroom.{id}', function ($user, $id) {
    if (!$user->student) return false;
    
    return $user->student->classrooms()->where('classrooms.id', $id)->exists();
});
