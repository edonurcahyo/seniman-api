<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pelanggan;

class ReservasiController extends Controller
{
    public function myReservations(Request $request)
    {
        $pelangganId = $request->input('pelanggan_id');
        
        if (!$pelangganId) {
            $user = $request->user();
            if ($user) {
                $pelangganId = $user->id_pelanggan;
            }
        }
        
        if (!$pelangganId) {
            return response()->json([
                'success' => false,
                'message' => 'pelanggan_id required',
                'data' => []
            ], 400);
        }

        $reservations = DB::table('reservasi as r')
            ->join('layanan as l', 'r.layanan_id', '=', 'l.id_layanan')
            ->leftJoin('slot_waktu as sw', 'r.slot_id', '=', 'sw.id_slot')
            ->leftJoin('cabang as c', 'r.cabang_id', '=', 'c.id_cabang')
            ->where('r.pelanggan_id', $pelangganId)
            ->select(
                'r.id_reservasi',
                'r.pelanggan_id',
                'r.cabang_id',
                'r.layanan_id',
                'r.slot_id',
                'r.tanggal_reservasi',
                'r.total_harga',
                'r.status',
                'r.metode_pembayaran',  
                'r.catatan',          
                'r.created_at',
                'l.nama_layanan',
                'l.durasi',
                'sw.jam_mulai',
                'c.nama_cabang',
                'c.alamat as cabang_alamat'
            )
            ->orderBy('r.created_at', 'desc')
            ->get();

        $result = [];
        foreach ($reservations as $item) {
            $result[] = [
                'id_reservasi' => $item->id_reservasi,
                'kode_reservasi' => 'RES-' . str_pad($item->id_reservasi, 6, '0', STR_PAD_LEFT),
                'pelanggan_id' => $item->pelanggan_id,
                'cabang_id' => $item->cabang_id,
                'nama_cabang' => $item->nama_cabang ?? 'Seniman Barbershop',
                'cabang_alamat' => $item->cabang_alamat ?? '',
                'layanan_id' => $item->layanan_id,
                'nama_layanan' => $item->nama_layanan,
                'durasi' => $item->durasi . ' menit',
                'total_harga' => (float) $item->total_harga,
                'tanggal' => $item->tanggal_reservasi,
                'waktu' => $item->jam_mulai ? substr($item->jam_mulai, 0, 5) : '10:00',
                'status' => $this->mapStatus($item->status),
                'metode_pembayaran' => $item->metode_pembayaran, // 🔥 UBAH: ambil dari database, bukan hardcode 'cash'
                'catatan' => $item->catatan,  // 🔥 TAMBAHKAN juga catatan
                'created_at' => $item->created_at
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id_pelanggan',
            'cabang_id' => 'required',
            'layanan_id' => 'required|exists:layanan,id_layanan',
            'slot_id' => 'required|exists:slot_waktu,id_slot',
            'tanggal_reservasi' => 'required|date',
            'total_harga' => 'required|numeric',
            'status_pembayaran' => 'nullable|string',
            'metode_pembayaran' => 'nullable|string',
            'catatan' => 'nullable|string'
        ]);

        $kodeReservasi = 'RES-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        $status = $request->status_pembayaran === 'paid' ? 'dikonfirmasi' : 'pending';

        $id = DB::table('reservasi')->insertGetId([
            'kode_reservasi' => $kodeReservasi,
            'pelanggan_id' => $request->pelanggan_id,
            'cabang_id' => $request->cabang_id,
            'layanan_id' => $request->layanan_id,
            'slot_id' => $request->slot_id,
            'tanggal_reservasi' => $request->tanggal_reservasi,
            'total_harga' => $request->total_harga,
            'metode_pembayaran' => $request->metode_pembayaran,
            'catatan' => $request->catatan,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Update slot status
        DB::table('slot_waktu')
            ->where('id_slot', $request->slot_id)
            ->update(['status' => 'dibooking']);

        return response()->json([
            'success' => true,
            'message' => 'Reservasi berhasil',
            'status' => $status,
            'id_reservasi' => $id,
            'kode_reservasi' => $kodeReservasi
        ]);
    }

    public function slots($tanggal)
    {
        // 🔥 Cari jadwal berdasarkan tanggal
        $jadwal = DB::table('jadwal')
            ->where('tanggal', $tanggal)
            ->first();

        // 🔥 Jika jadwal tidak ditemukan, buat jadwal baru untuk tanggal tersebut
        if (!$jadwal) {
            // Buat jadwal baru
            $jadwalId = DB::table('jadwal')->insertGetId([
                'tanggal' => $tanggal,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Buat slot waktu untuk jadwal baru (default 10:00 - 22:00)
            $jamMulai = 10;
            $jamSelesai = 22;
            
            for ($jam = $jamMulai; $jam <= $jamSelesai; $jam++) {
                DB::table('slot_waktu')->insert([
                    'jadwal_id' => $jadwalId,
                    'jam_mulai' => sprintf('%02d:00:00', $jam),
                    'jam_selesai' => sprintf('%02d:00:00', $jam + 1),
                    'status' => 'tersedia',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            $slots = DB::table('slot_waktu')
                ->where('jadwal_id', $jadwalId)
                ->get();
                
            return response()->json($slots);
        }

        // 🔥 Ambil slot berdasarkan jadwal_id
        $slots = DB::table('slot_waktu')
            ->where('jadwal_id', $jadwal->id_jadwal)
            ->get();

        return response()->json($slots);
    }


    public function dashboardStats()
    {
        $totalReservations = DB::table('reservasi')->count();
        $todayReservations = DB::table('reservasi')->whereDate('tanggal_reservasi', now())->count();
        $totalCustomers = DB::table('pelanggan')->count();
        $monthlyRevenue = DB::table('reservasi')
            ->whereMonth('tanggal_reservasi', now()->month)
            ->whereYear('tanggal_reservasi', now()->year)
            ->where('status', 'dikonfirmasi')
            ->sum('total_harga');
        
        return response()->json([
            'totalReservations' => $totalReservations,
            'todayReservations' => $todayReservations,
            'totalCustomers' => $totalCustomers,
            'monthlyRevenue' => (float) $monthlyRevenue,
            'revenueGrowth' => 15.3
        ]);
    }

    public function allReservations()
    {
        $reservations = DB::table('reservasi as r')
            ->leftJoin('pelanggan as p', 'r.pelanggan_id', '=', 'p.id_pelanggan')
            ->leftJoin('layanan as l', 'r.layanan_id', '=', 'l.id_layanan')
            ->leftJoin('cabang as c', 'r.cabang_id', '=', 'c.id_cabang')
            ->leftJoin('slot_waktu as sw', 'r.slot_id', '=', 'sw.id_slot')
            ->select(
                'r.id_reservasi',
                'r.kode_reservasi',
                'r.pelanggan_id',
                'p.nama as pelanggan_nama',
                'r.cabang_id',
                'c.nama_cabang as cabang_nama',
                'r.layanan_id',
                'l.nama_layanan as layanan_nama',
                'r.tanggal_reservasi',
                'sw.jam_mulai as waktu',
                'r.total_harga',
                'r.status',
                'r.metode_pembayaran',
                'r.created_at'
            )
            ->orderBy('r.created_at', 'desc')
            ->get();
        
        // Add waktu from jam_mulai
        foreach ($reservations as $r) {
            if ($r->waktu) {
                $r->waktu = substr($r->waktu, 0, 5);
            }
        }
        
        return response()->json(['data' => $reservations]);
    }

    public function allCustomers()
    {
        $customers = DB::table('pelanggan')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json(['data' => $customers]);
    }

    public function updateReservationStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,dikonfirmasi,selesai,dibatalkan'
        ]);
        
        DB::table('reservasi')
            ->where('id_reservasi', $id)
            ->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);
        
        return response()->json(['success' => true]);
    }

    public function profile(Request $request)
    {
        $pelangganId = $request->input('pelanggan_id');
        
        if (!$pelangganId) {
            $user = $request->user();
            if ($user) {
                $pelangganId = $user->id_pelanggan;
            }
        }
        
        if (!$pelangganId) {
            return response()->json([
                'success' => false,
                'message' => 'pelanggan_id required'
            ], 400);
        }

        $user = DB::table('pelanggan')
            ->where('id_pelanggan', $pelangganId)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'id_pelanggan' => $user->id_pelanggan,
            'nama' => $user->nama,
            'email' => $user->email,
            'no_hp' => $user->no_hp,
            'alamat' => $user->alamat,
            'created_at' => $user->created_at
        ]);
    }

    public function updateProfile(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pelanggan,email,' . $id . ',id_pelanggan',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'nullable|string'
        ]);

        DB::table('pelanggan')
            ->where('id_pelanggan', $id)
            ->update([
                'nama' => $request->nama,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'updated_at' => now()
            ]);

        $updatedUser = DB::table('pelanggan')->where('id_pelanggan', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $updatedUser
        ]);
    }

    private function mapStatus($status)
    {
        $statusMap = [
            'pending' => 'pending',
            'dikonfirmasi' => 'paid',
            'selesai' => 'paid',
            'dibatalkan' => 'cancelled'
        ];
        return $statusMap[$status] ?? 'pending';
    }
}