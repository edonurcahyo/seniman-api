<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $table = 'payment_settings';
    protected $fillable = [
        'cabang_id',
        'bank_bca',
        'bank_mandiri',
        'bank_bni',
        'bank_bri',
        'qr_code'
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id_cabang');
    }
}