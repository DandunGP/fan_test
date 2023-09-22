<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Epresence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'waktu',
        'is_approve',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function epresence(){
        return $this->belongsTo(Epresence::class, 'waktu');
    }
}
