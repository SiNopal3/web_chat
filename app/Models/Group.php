<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    // Buka gembok keamanan seperti kemarin
    protected $fillable = ['name', 'creator_id'];

    // Relasi: Satu grup punya banyak anggota (user)
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // Relasi: Satu grup punya banyak pesan chat
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}