<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Gembok Hari 2: Radar Online (Presence Channel)
Broadcast::channel('online-users', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

// Gembok Hari 3: Private Chat 1-on-1 (Private Channel)
Broadcast::channel('chat.{id1}.{id2}', function ($user, $id1, $id2) {
    return (int) $user->id === (int) $id1 || (int) $user->id === (int) $id2;
});

// Gembok Hari 4: Grup Chat (Presence Channel)
Broadcast::channel('group.{id}', function ($user, $id) {
    // Cek apakah user ada di dalam daftar anggota grup
    if ($user->groups->contains($id)) {
        return ['id' => $user->id, 'name' => $user->name];
    }
    return false;
});