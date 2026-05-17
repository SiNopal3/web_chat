<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Fungsi untuk mengambil riwayat chat lama
    public function fetchMessages(User $user)
    {
        $messages = Message::where(function($q) use ($user) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $user->id);
        })->orWhere(function($q) use ($user) {
            $q->where('sender_id', $user->id)->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    // Fungsi untuk menyimpan pesan baru dan menyiarkannya
    public function sendMessage(Request $request, User $user)
    {
        $request->validate(['message' => 'required|string']);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'message' => $request->message,
        ]);

        // Siarkan pesannya ke temanmu pakai Reverb!
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }
}