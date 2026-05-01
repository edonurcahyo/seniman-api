<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    use HasFactory;

    protected $table = 'reservasi';
    protected $primaryKey = 'id_reservasi';

    protected $fillable = [
        'pelanggan_id',
        'cabang_id',
        'layanan_id',
        'slot_id',
        'tanggal_reservasi',
        'total_harga',
        'metode_pembayaran',
        'status',
        'bukti_pembayaran',
        'catatan'
    ];

    // Relasi ke Pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'id_pelanggan');
    }

    // Relasi ke Layanan
    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id', 'id_layanan');
    }

    // Relasi ke Slot
    public function slot()
    {
        return $this->belongsTo(SlotWaktu::class, 'slot_id', 'id_slot');
    }

    // Accessor untuk status dalam bahasa Indonesia
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Menunggu Pembayaran',
            'dikonfirmasi' => 'Lunas',
            'cancelled' => 'Dibatalkan'
        ];
        return $statuses[$this->status] ?? $this->status;
    }
}