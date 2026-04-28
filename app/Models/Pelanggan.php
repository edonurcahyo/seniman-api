<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pelanggan extends Authenticatable
{
    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'alamat'
    ];

    protected $hidden = [
        'password'
    ];

    public $timestamps = true;
}