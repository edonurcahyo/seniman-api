<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $table = 'cabang';
    protected $primaryKey = 'id_cabang';

    protected $fillable = [
        'nama_cabang',
        'alamat',
        'no_hp',
        'jam_buka',
        'jam_tutup',
        'status'
    ];

    public $timestamps = true;

    // Relasi ke payment settings
    public function paymentSettings()
    {
        return $this->hasOne(PaymentSetting::class, 'cabang_id', 'id_cabang');
    }
}