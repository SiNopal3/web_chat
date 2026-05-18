<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // Gembok sudah dibuka untuk semuanya
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'group_id',
        'message',
    ];

    // Relasi untuk memunculkan nama pengirim di Grup Chat
    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relasi ke tabel grup
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}