<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    // Mengambil daftar semua grup
    public function index()
    {
        $groups = Group::all();
        return response()->json($groups);
    }

    // Membuat grup baru
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        // Tambahkan creator_id ke dalam sini
        $group = Group::create([
            'name' => $request->name,
            'creator_id' => auth()->id() 
        ]);
        
        // Otomatis masukkan si pembuat grup sebagai anggota pertama
        $group->users()->attach(auth()->id());

        return response()->json($group);
    }

    // Bergabung ke grup
    public function join(Group $group)
    {
        // Cek kalau belum bergabung, baru gabungkan
        if (!$group->users->contains(auth()->id())) {
            $group->users()->attach(auth()->id());
        }
        return response()->json(['message' => 'Berhasil bergabung!']);
    }

    // Mengambil riwayat chat khusus di dalam grup tersebut
    public function fetchMessages(Group $group)
    {
        // Ambil pesannya sekalian bawa data user (pengirimnya)
        $messages = $group->messages()->with('user')->orderBy('created_at', 'asc')->get();
        return response()->json($messages);
    }
    // Fungsi untuk menyimpan pesan grup dan menyiarkannya
    public function sendMessage(Request $request, Group $group)
    {
        $request->validate(['message' => 'required|string']);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'group_id' => $group->id, // Menggunakan group_id, bukan receiver_id
            'message' => $request->message,
        ]);

        // Siarkan pesannya ke seluruh anggota grup!
        broadcast(new \App\Events\GroupMessageSent($message))->toOthers();

        return response()->json($message->load('user'));
    }
}