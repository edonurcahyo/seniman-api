<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';
    protected $primaryKey = 'id_layanan';

    protected $fillable = [
        'kode_layanan',
        'nama_layanan',
        'harga',
        'durasi',
        'deskripsi',
        'status',
        'gambar'
    ];
}