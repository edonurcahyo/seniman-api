<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlotWaktu extends Model
{
    use HasFactory;

    protected $table = 'slot_waktu';
    protected $primaryKey = 'id_slot';
    public $timestamps = false;

    protected $fillable = [
        'jadwal_id',
        'jam_mulai',
        'jam_selesai',
        'status'
    ];
}