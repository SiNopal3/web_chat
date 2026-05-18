<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Message;
use App\Events\GroupMessageSent;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    // Mengambil daftar grup
    public function index()
    {
        return response()->json(Group::all());
    }

    // Membuat grup baru
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        $group = Group::create([
            'name' => $request->name,
            'creator_id' => auth()->id() 
        ]);
        
        // Otomatis masukkan si pembuat grup sebagai anggota
        $group->users()->attach(auth()->id());

        return response()->json($group);
    }

    // Fungsi untuk JOIN ke dalam grup saat diklik
    public function join(Group $group)
    {
        // Cek jika user belum ada di dalam grup, maka masukkan
        if (!$group->users->contains(auth()->id())) {
            $group->users()->attach(auth()->id());
        }
        return response()->json(['message' => 'Berhasil join grup!']);
    }

    // Fungsi mengambil riwayat chat grup
    public function fetchMessages(Group $group)
    {
        // Ambil pesan beserta data 'user' (pengirimnya) biar namanya muncul
        return response()->json($group->messages()->with('user')->get());
    }

    // Fungsi mengirim pesan ke grup
    public function sendMessage(Request $request, Group $group)
    {
        $request->validate(['message' => 'required|string']);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'group_id' => $group->id,
            'message' => $request->message,
            'receiver_id' => null, // Dikosongkan karena ini chat grup
        ]);

        // Siarkan pesan grupnya lewat Reverb!
        broadcast(new GroupMessageSent($message))->toOthers();

        return response()->json($message);
    }
}