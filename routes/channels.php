<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// LMS - Notifikasi materi ke kelas
Broadcast::channel('classroom.{id}', function ($user, $id) {
    if (!$user->student) return false;
    
    return $user->student->classrooms()->where('classrooms.id', $id)->exists();
});
