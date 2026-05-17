<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // INI DIA KUNCI GEMBOKNYA! Mengizinkan kolom ini diisi oleh user
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'group_id',
        'message',
    ];
}