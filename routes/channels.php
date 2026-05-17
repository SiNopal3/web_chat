<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('online-users', function ($user) {
    // Kode ini akan mengembalikan data user (ID & Nama) ke frontend jika dia terautentikasi
    return ['id' => $user->id, 'name' => $user->name];
});