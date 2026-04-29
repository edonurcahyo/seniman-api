<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservasiController extends Controller
{
    public function slots($tanggal)
    {
        $jadwal = DB::table('jadwal')
            ->where('tanggal', $tanggal)
            ->first();

        if (!$jadwal) {
            return response()->json([]);
        }

        $slot = DB::table('slot_waktu')
            ->where('jadwal_id', $jadwal->id_jadwal)
            ->get();

        return response()->json($slot);
    }

    public function store(Request $request)
    {
        $status = $request->metode_bayar == 'nanti'
            ? 'pending'
            : 'dikonfirmasi';

        $id = DB::table('reservasi')->insertGetId([
            'pelanggan_id' => $request->pelanggan_id,
            'cabang_id' => $request->cabang_id,
            'layanan_id' => $request->layanan_id,
            'slot_id' => $request->slot_id,
            'tanggal_reservasi' => $request->tanggal_reservasi,
            'total_harga' => $request->total_harga,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('slot_waktu')
            ->where('id_slot', $request->slot_id)
            ->update(['status' => 'dibooking']);

        return response()->json([
            'success' => true,
            'message' => 'Reservasi berhasil',
            'status' => $status,
            'id_reservasi' => $id
        ]);
    }
}