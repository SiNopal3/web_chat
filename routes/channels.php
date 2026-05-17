<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('online-users', function ($user) {
    // Kode ini akan mengembalikan data user (ID & Nama) ke frontend jika dia terautentikasi
    return ['id' => $user->id, 'name' => $user->name];
});
Broadcast::channel('chat.{id1}.{id2}', function ($user, $id1, $id2) {
    // Hanya user ID 1 dan ID 2 yang boleh masuk ke ruangan chat ini
    return (int) $user->id === (int) $id1 || (int) $user->id === (int) $id2;
});