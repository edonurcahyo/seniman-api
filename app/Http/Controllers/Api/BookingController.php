<!-- <?php

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\Jadwal;
// use App\Models\SlotWaktu;
// use App\Models\Reservasi;

// class BookingController extends Controller
// {
//     public function slots($tanggal)
//     {
//         $jadwal = Jadwal::where('tanggal', $tanggal)->first();

//         if (!$jadwal) {
//             return response()->json([]);
//         }

//         $slots = SlotWaktu::where('jadwal_id', $jadwal->id_jadwal)->get();

//         return response()->json($slots);
//     }

//     public function store(Request $request)
//     {
//         $reservasi = Reservasi::create([
//             'pelanggan_id' => $request->pelanggan_id,
//             'cabang_id' => $request->cabang_id,
//             'layanan_id' => $request->layanan_id,
//             'slot_id' => $request->slot_id,
//             'tanggal_reservasi' => $request->tanggal,
//             'total_harga' => $request->total_harga,
//             'status' => 'pending'
//         ]);

//         SlotWaktu::where('id_slot', $request->slot_id)
//             ->update(['status' => 'dibooking']);

//         return response()->json([
//             'message' => 'Booking berhasil',
//             'data' => $reservasi
//         ]);
//     }
// }